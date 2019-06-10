<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Tests\Form\DataMapper;

use eZ\Publish\API\Repository\Values\Content\SectionCreateStruct;
use eZ\Publish\API\Repository\Values\Content\LocationCreateStruct;
use EzSystems\EzPlatformAdminUi\Form\Data\Language\LanguageCreateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Section\SectionCreateData;
use EzSystems\EzPlatformAdminUi\Form\DataMapper\SectionCreateMapper;
use PHPUnit\Framework\TestCase;
use EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException;

class SelectionCreateMapperTest extends TestCase
{
    /** @var SectionCreateMapper */
    private $mapper;

    protected function setUp(): void
    {
        $this->mapper = new SectionCreateMapper();
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
        $this->expectExceptionMessage('Argument \'value\' is invalid: must be an instance of ' . SectionCreateStruct::class);

        $this->mapper->map(new LocationCreateStruct());
    }

    public function testReverseMapWithWrongInstance()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument \'data\' is invalid: must be an instance of ' . SectionCreateData::class);

        $this->mapper->reverseMap(new LanguageCreateData());
    }

    public function dataProvider(): array
    {
        return [
            'simple' => [['identifier' => 'hash', 'name' => 'Lorem']],
            'without_name' => [['identifier' => 'hash', 'name' => null]],
            'without_identifier' => [['identifier' => null, 'name' => 'Lorem']],
            'with_null' => [['identifier' => null, 'name' => null]],
        ];
    }

    /**
     * @param array $properties
     *
     * @return SectionCreateStruct
     */
    private function createStruct(array $properties): SectionCreateStruct
    {
        return new SectionCreateStruct($properties);
    }

    /**
     * @param array $properties
     *
     * @return SectionCreateData
     */
    private function createData(array $properties): SectionCreateData
    {
        return new SectionCreateData($properties['identifier'], $properties['name']);
    }
}
