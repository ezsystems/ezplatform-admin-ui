<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Tests\Form\DataMapper;

use eZ\Publish\API\Repository\Values\Content\LanguageCreateStruct;
use eZ\Publish\API\Repository\Values\Content\LocationCreateStruct;
use EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException;
use EzSystems\EzPlatformAdminUi\Form\Data\Language\LanguageCreateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Language\LanguageDeleteData;
use EzSystems\EzPlatformAdminUi\Form\DataMapper\LanguageCreateMapper;
use PHPUnit\Framework\TestCase;

class LanguageCreateMapperTest extends TestCase
{
    /** @var \EzSystems\EzPlatformAdminUi\Form\DataMapper\LanguageCreateMapper */
    private $mapper;

    protected function setUp(): void
    {
        $this->mapper = new LanguageCreateMapper();
    }

    protected function tearDown(): void
    {
        unset($this->mapper);
    }

    /**
     * @dataProvider dataProvider
     *
     * @param array $properties
     */
    public function testMap(array $properties)
    {
        $data = $this->mapper->map($this->createStruct($properties));

        $this->assertEquals($this->createData($properties), $data);
    }

    /**
     * @dataProvider dataProvider
     *
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
        $this->expectExceptionMessage('Argument \'value\' is invalid: must be an instance of ' . LanguageCreateStruct::class);

        $this->mapper->map(new LocationCreateStruct());
    }

    public function testReverseMapWithWrongInstance()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument \'data\' is invalid: must be an instance of ' . LanguageCreateData::class);

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
     *
     * @return \eZ\Publish\API\Repository\Values\Content\LanguageCreateStruct
     */
    private function createStruct(array $properties): LanguageCreateStruct
    {
        return new LanguageCreateStruct($properties);
    }

    /**
     * @param array $properties
     *
     * @return \EzSystems\EzPlatformAdminUi\Form\Data\Language\LanguageCreateData
     */
    private function createData(array $properties): LanguageCreateData
    {
        return (new LanguageCreateData())
            ->setLanguageCode($properties['languageCode'])
            ->setName($properties['name'])
            ->setEnabled($properties['enabled']);
    }
}
