<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\Component\Fields;

use Ibexa\Behat\Browser\Locator\CSSLocatorBuilder;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use PHPUnit\Framework\Assert;

class MapLocation extends FieldTypeComponent
{
    private const OPEN_STREET_MAP_TIMEOUT = 20;

    public function setValue(array $parameters): void
    {
        $this->setSpecificCoordinate('address', $parameters['address']);
        $this->getHTMLPage()->find($this->getLocator('searchButton'))->click();

        $expectedLongitude = $parameters['longitude'];
        $expectedLatitude = $parameters['latitude'];

        $this->getHTMLPage()->setTimeout(self::OPEN_STREET_MAP_TIMEOUT)->waitUntil(
            function () use ($expectedLatitude, $expectedLongitude) {
                $currentValue = $this->getValue();

                return $currentValue['latitude'] === $expectedLatitude && $currentValue['longitude'] === $expectedLongitude;
            },
            'Failed to verify OpenStreetMaps data.'
        );
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
        $coordinateSelector = CSSLocatorBuilder::base($this->parentLocator)
            ->withDescendant($this->getLocator($coordinateName))
            ->build()
        ;

        return $this->getHTMLPage()->find($coordinateSelector)->getValue();
    }

    public function verifyValueInEditView(array $value): void
    {
        $expectedLatitude = $value['latitude'];
        $expectedLongitude = $value['longitude'];
        $expectedAddress = $value['address'];

        Assert::assertEquals(
            $expectedLatitude,
            $this->getValue()['latitude'],
            sprintf('Field %s has wrong latitude value', $value['label'])
        );
        Assert::assertEquals(
            $expectedLongitude,
            $this->getValue()['longitude'],
            sprintf('Field %s has wrong longitude value', $value['label'])
        );
        Assert::assertEquals(
            $expectedAddress,
            $this->getValue()['address'],
            sprintf('Field %s has wrong address value', $value['label'])
        );
    }

    public function verifyValueInItemView(array $expectedValues): void
    {
        $mapText = $this->getHTMLPage()->find($this->parentLocator)->getText();

        $matches = [];
        preg_match('/Address: (.*) Latitude: (.*) Longitude: (.*)/', $mapText, $matches);

        $actualAddress = $matches[1];
        $actualLatitude = $this->formatToOneDecimalPlace($matches[2]);
        $actualLongitude = $this->formatToOneDecimalPlace($matches[3]);

        Assert::assertEquals($expectedValues['address'], $actualAddress);
        Assert::assertEquals($expectedValues['latitude'], $actualLatitude);
        Assert::assertEquals($expectedValues['longitude'], $actualLongitude);
    }

    public function getFieldTypeIdentifier(): string
    {
        return 'ezgmaplocation';
    }

    public function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('latitude', '#ezplatform_content_forms_content_edit_fieldsData_ezgmaplocation_value_latitude'),
            new VisibleCSSLocator('longitude', '#ezplatform_content_forms_content_edit_fieldsData_ezgmaplocation_value_longitude'),
            new VisibleCSSLocator('address', '#ezplatform_content_forms_content_edit_fieldsData_ezgmaplocation_value_address'),
            new VisibleCSSLocator('searchButton', '.ibexa-btn--search-by-address'),
        ];
    }

    private function setSpecificCoordinate(string $coordinateName, string $value): void
    {
        $fieldSelector = CSSLocatorBuilder::base($this->parentLocator)
            ->withDescendant($this->getLocator($coordinateName))
            ->build()
        ;
        $this->getHTMLPage()->find($fieldSelector)->setValue($value);
    }

    private function formatToOneDecimalPlace(string $value): string
    {
        $number = (float) $value;
        $formattedNumber = number_format($number, 1);

        return sprintf('%.1f', $formattedNumber);
    }
}
