<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Tests\Form\DataMapper;

use eZ\Publish\API\Repository\Values\Content\LanguageCreateStruct;
use eZ\Publish\API\Repository\Values\Content\LocationCreateStruct;
use EzSystems\EzPlatformAdminUi\Form\Data\Language\LanguageCreateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Language\LanguageDeleteData;
use EzSystems\EzPlatformAdminUi\Form\DataMapper\LanguageCreateMapper;
use PHPUnit\Framework\TestCase;
use EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException;

class LanguageCreateMapperTest extends TestCase
{
    /**
     * @var LanguageCreateMapper
     */
    private $mapper;

    public function setUp()
    {
        $this->mapper = new LanguageCreateMapper();
    }

    public function tearDown()
    {
        unset($this->mapper);
    }

    /**
     * @dataProvider dataProvider
     * @param array $properties
     */
    public function testMap(array $properties)
    {
        $data = $this->mapper->map($this->createStruct($properties));

        $this->assertEquals($this->createData($properties), $data);
    }

    /**
     * @dataProvider dataProvider
     * @param array $properties
     */
    public function testReverseMap(array $properties)
    {
        $struct = $this->mapper->reverseMap($this->createData($properties));

        $this->assertEquals($this->createStruct($properties), $struct);
    }

    public function testMapWithWrongInstance()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument \'value\' is invalid: must be instance of ' . LanguageCreateStruct::class);
        $this->mapper->map(new LocationCreateStruct());
    }

    public function testReverseMapWithWrongInstance()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument \'data\' is invalid: must be instance of ' . LanguageCreateData::class);
        $this->mapper->reverseMap(new LanguageDeleteData());
    }

    public function dataProvider()
    {
        return [
            'enabled_true' => [['languageCode' => 'AB', 'name' => 'Lorem', 'enabled' => true]],
            'enabled_false' => [['languageCode' => 'CD', 'name' => 'Ipsum', 'enabled' => false]],
        ];
    }

    /**
     * @param array $properties
     * @return LanguageCreateStruct
     */
    private function createStruct(array $properties): LanguageCreateStruct
    {
        return new LanguageCreateStruct($properties);
    }

    /**
     * @param $properties
     * @return LanguageCreateData
     */
    private function createData(array $properties): LanguageCreateData
    {
        return (new LanguageCreateData())
            ->setLanguageCode($properties['languageCode'])
            ->setName($properties['name'])
            ->setEnabled($properties['enabled']);
    }
}
