<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace EzSystems\EzPlatformAdminUi\Tests\Form\DataTransformer;

use eZ\Publish\Core\FieldType\Country\Value;
use EzSystems\EzPlatformAdminUi\Form\DataTransformer\FieldType\MultipleCountryValueTransformer;
use PHPUnit\Framework\TestCase;

class MultipleCountryValueTransformerTest extends TestCase
{
    /**
     * @var array Array of countries from ezpublish.fieldType.ezcountry.data
     */
    protected $countriesInfo = [
        'AF' => ['Name' => 'Afghanistan', 'Alpha2' => 'AF', 'Alpha3' => 'AFG', 'IDC' => '93'],
        'AX' => ['Name' => 'Åland', 'Alpha2' => 'AX', 'Alpha3' => 'ALA', 'IDC' => '358'],
        'AQ' => ['Name' => 'Antarctica', 'Alpha2' => 'AQ', 'Alpha3' => 'ATA', 'IDC' => '672'],
        'AG' => ['Name' => 'Antigua and Barbuda', 'Alpha2' => 'AG', 'Alpha3' => 'ATG', 'IDC' => '1268'],
        'AM' => ['Name' => 'Armenia', 'Alpha2' => 'AM', 'Alpha3' => 'ARM', 'IDC' => '374'],
        'BB' => ['Name' => 'Barbados', 'Alpha2' => 'BB', 'Alpha3' => 'BRB', 'IDC' => '1246'],
        'BJ' => ['Name' => 'Benin', 'Alpha2' => 'BJ', 'Alpha3' => 'BEN', 'IDC' => '229'],
        'BM' => ['Name' => 'Bermuda', 'Alpha2' => 'BM', 'Alpha3' => 'BMU', 'IDC' => '1441'],
        'BT' => ['Name' => 'Bhutan', 'Alpha2' => 'BT', 'Alpha3' => 'BTN', 'IDC' => '975'],
        'BA' => ['Name' => 'Bosnia and Herzegovina', 'Alpha2' => 'BA', 'Alpha3' => 'BIH', 'IDC' => '387'],
        'BW' => ['Name' => 'Botswana', 'Alpha2' => 'BW', 'Alpha3' => 'BWA', 'IDC' => '267'],
        'BV' => ['Name' => 'Bouvet Island', 'Alpha2' => 'BV', 'Alpha3' => 'BVT', 'IDC' => '47'],
        'IO' => ['Name' => 'British Indian Ocean Territory', 'Alpha2' => 'IO', 'Alpha3' => 'IOT', 'IDC' => '246'],
        'BN' => ['Name' => 'Brunei Darussalam', 'Alpha2' => 'BN', 'Alpha3' => 'BRN', 'IDC' => '673'],
        'BF' => ['Name' => 'Burkina Faso', 'Alpha2' => 'BF', 'Alpha3' => 'BFA', 'IDC' => '226'],
        'KH' => ['Name' => 'Cambodia', 'Alpha2' => 'KH', 'Alpha3' => 'KHM', 'IDC' => '855'],
        'CV' => ['Name' => 'Cape Verde', 'Alpha2' => 'CV', 'Alpha3' => 'CPV', 'IDC' => '238'],
        'CF' => ['Name' => 'Central African Republic', 'Alpha2' => 'CF', 'Alpha3' => 'CAF', 'IDC' => '236'],
        'CN' => ['Name' => 'China', 'Alpha2' => 'CN', 'Alpha3' => 'CHN', 'IDC' => '86'],
        'CC' => ['Name' => 'Cocos (Keeling) Islands', 'Alpha2' => 'CC', 'Alpha3' => 'CCK', 'IDC' => '61'],
        'BL' => ['Name' => 'Saint Barthélemy', 'Alpha2' => 'BL', 'Alpha3' => 'BLM', 'IDC' => '590'],
        'GS' => ['Name' => 'South Georgia and The South Sandwich Islands', 'Alpha2' => 'GS', 'Alpha3' => 'SGS', 'IDC' => '500'],
        'TW' => ['Name' => 'Taiwan', 'Alpha2' => 'TW', 'Alpha3' => 'TWN', 'IDC' => '886'],
        'ZW' => ['Name' => 'Zimbabwe', 'Alpha2' => 'ZW', 'Alpha3' => 'ZWE', 'IDC' => '263'],
    ];

    public function transformProvider()
    {
        return [
            [[
                  'BN' => ['Name' => 'Brunei Darussalam', 'Alpha2' => 'BN', 'Alpha3' => 'BRN', 'IDC' => '673'],
              ]],
            [[
                  'AX' => ['Name' => 'Åland', 'Alpha2' => 'AX', 'Alpha3' => 'ALA', 'IDC' => '358'],
                  'BL' => ['Name' => 'Saint Barthélemy', 'Alpha2' => 'BL', 'Alpha3' => 'BLM', 'IDC' => '590'],
                  'GS' => ['Name' => 'South Georgia and The South Sandwich Islands', 'Alpha2' => 'GS', 'Alpha3' => 'SGS', 'IDC' => '500'],
              ]],
        ];
    }

    /**
     * @dataProvider transformProvider
     */
    public function testTransform($valueAsArray)
    {
        $transformer = new MultipleCountryValueTransformer($this->countriesInfo);
        $value = new Value($valueAsArray);
        self::assertSame(array_keys($valueAsArray), $transformer->transform($value));
    }

    /**
     * @dataProvider transformProvider
     */
    public function testReverseTransform($valueAsArray)
    {
        $transformer = new MultipleCountryValueTransformer($this->countriesInfo);
        $expectedValue = new Value($valueAsArray);
        self::assertEquals($expectedValue, $transformer->reverseTransform(array_keys($valueAsArray)));
    }

    public function transformNullProvider()
    {
        return [
            [42],
            ['snafu'],
            [null],
            [[1, 2, 3]],
        ];
    }

    /**
     * @dataProvider transformNullProvider
     */
    public function testTransformNull($value)
    {
        $transformer = new MultipleCountryValueTransformer($this->countriesInfo);
        self::assertNull($transformer->transform($value));
    }

    public function reverseTransformNullProvider()
    {
        return [
            [42],
            ['snafu'],
            [null],
            [new \eZ\Publish\Core\FieldType\Country\Value()],
        ];
    }

    /**
     * @dataProvider reverseTransformNullProvider
     */
    public function testReverseTransformNull($value)
    {
        $transformer = new MultipleCountryValueTransformer($this->countriesInfo);
        self::assertNull($transformer->reverseTransform($value));
    }
}
