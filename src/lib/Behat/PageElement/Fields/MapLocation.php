<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields;

use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;
use PHPUnit\Framework\Assert;

class MapLocation extends EzFieldElement
{
    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'Map location';

    private const OPEN_STREET_MAP_TIMEOUT = 20;

    public function __construct(UtilityContext $context, string $locator, string $label)
    {
        parent::__construct($context, $locator, $label);
        $this->fields['latitude'] = '#ezrepoforms_content_edit_fieldsData_ezgmaplocation_value_latitude';
        $this->fields['longitude'] = '#ezrepoforms_content_edit_fieldsData_ezgmaplocation_value_longitude';
        $this->fields['address'] = '#ezrepoforms_content_edit_fieldsData_ezgmaplocation_value_address';
        $this->fields['searchButton'] = '.btn--search-by-address';
    }

    public function setValue(array $parameters): void
    {
        $this->setSpecificCoordinate('address', $parameters['address']);
        $this->context->findElement($this->fields['searchButton'])->click();

        // wait until OpenStreetMap respondes with data
        $expectedLongitude = $parameters['longitude'];
        $expectedLatitude = $parameters['latitude'];

        $this->context->waitUntil(self::OPEN_STREET_MAP_TIMEOUT, function () use ($expectedLatitude, $expectedLongitude) {
            $currentValue = $this->getValue();

            return $currentValue['latitude'] === $expectedLatitude && $currentValue['longitude'] === $expectedLongitude;
        });
    }

    public function getValue(): array
    {
        return [
            'latitude' => $this->formatToOneDecimalPlace($this->getSpecificCoordinate('latitude')),
            'longitude' => $this->formatToOneDecimalPlace($this->getSpecificCoordinate('longitude')),
            'address' => $this->getSpecificCoordinate('address'),
            ];
    }

    public function getSpecificCoordinate(string $coordinateName): string
    {
        $fieldInput = $this->context->findElement(
            sprintf('%s %s', $this->fields['fieldContainer'], $this->fields[$coordinateName])
        );

        Assert::assertNotNull($fieldInput, sprintf('Input for field %s not found.', $this->label));

        return $fieldInput->getValue();
    }

    public function verifyValue(array $value): void
    {
        Assert::assertEquals(
            $value['latitude'],
            $this->getValue()['latitude'],
            sprintf('Field %s has wrong latitude value', $value['label'])
        );
        Assert::assertEquals(
            $value['longitude'],
            $this->getValue()['longitude'],
            sprintf('Field %s has wrong longitude value', $value['label'])
        );
        Assert::assertEquals(
            $value['address'],
            $this->getValue()['address'],
            sprintf('Field %s has wrong address value', $value['label'])
        );
    }

    public function verifyValueInItemView(array $values): void
    {
        Assert::assertStringEndsWith(
            sprintf('Address: %s Latitude: %s Longitude: %s', $values['address'], $values['latitude'], $values['longitude']),
            $this->context->findElement($this->fields['fieldContainer'])->getText(),
            'Field has wrong value'
        );
    }

    private function setSpecificCoordinate(string $coordinateName, string $value): void
    {
        $fieldInput = $this->context->findElement(
            sprintf('%s %s', $this->fields['fieldContainer'], $this->fields[$coordinateName])
        );

        Assert::assertNotNull($fieldInput, sprintf('Input %s for field %s not found.', $coordinateName, $this->label));

        $fieldInput->setValue('');
        $fieldInput->setValue($value);
    }

    private function formatToOneDecimalPlace(string $value): string
    {
        $number = (float) $value;
        $formattedNumber = number_format($number, 1);

        return sprintf('%f', $formattedNumber);
    }
}
