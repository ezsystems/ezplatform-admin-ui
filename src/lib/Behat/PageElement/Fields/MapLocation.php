<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields;

use EzSystems\Behat\Browser\Context\BrowserContext;
use PHPUnit\Framework\Assert;

class MapLocation extends EzFieldElement
{
    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'Map location';

    private const OPEN_STREET_MAP_TIMEOUT = 20;

    public function __construct(BrowserContext $context, string $locator, string $label)
    {
        parent::__construct($context, $locator, $label);
        $this->fields['latitude'] = '#ezplatform_content_forms_content_edit_fieldsData_ezgmaplocation_value_latitude';
        $this->fields['longitude'] = '#ezplatform_content_forms_content_edit_fieldsData_ezgmaplocation_value_longitude';
        $this->fields['address'] = '#ezplatform_content_forms_content_edit_fieldsData_ezgmaplocation_value_address';
        $this->fields['searchButton'] = '.btn--search-by-address';
    }

    public function setValue(array $parameters): void
    {
        $this->setSpecificCoordinate('address', $parameters['address']);
        $this->context->findElement($this->fields['searchButton'])->click();

        $expectedLongitude = $parameters['longitude'];
        $expectedLatitude = $parameters['latitude'];

        // wait until OpenStreetMap responds with data
        $this->context->waitUntil(self::OPEN_STREET_MAP_TIMEOUT, function () use ($expectedLatitude, $expectedLongitude) {
            $currentValue = $this->getValue();

            return $currentValue['latitude'] === $expectedLatitude && $currentValue['longitude'] === $expectedLongitude;
        });
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
        $mapText = $this->context->findElement($this->fields['fieldContainer'])->getText();

        $matches = [];
        preg_match('/Address: (.*) Latitude: (.*) Longitude: (.*)/', $mapText, $matches);

        $actualAddress = $matches[1];
        $actualLatitude = $this->formatToOneDecimalPlace($matches[2]);
        $actualLongitude = $this->formatToOneDecimalPlace($matches[3]);

        Assert::assertEquals($values['address'], $actualAddress);
        Assert::assertEquals($values['latitude'], $actualLatitude);
        Assert::assertEquals($values['longitude'], $actualLongitude);
    }

    private function formatToOneDecimalPlace(string $value): string
    {
        $number = (float) $value;
        $formattedNumber = number_format($number, 1);

        return sprintf('%.1f', $formattedNumber);
    }
}
