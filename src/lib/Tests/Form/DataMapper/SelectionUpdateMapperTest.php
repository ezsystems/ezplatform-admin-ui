<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Tests\Form\DataMapper;

use eZ\Publish\API\Repository\Values\Content\LocationCreateStruct;
use eZ\Publish\API\Repository\Values\Content\Section;
use eZ\Publish\API\Repository\Values\Content\SectionUpdateStruct;
use EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException;
use EzSystems\EzPlatformAdminUi\Form\Data\Language\LanguageCreateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Section\SectionUpdateData;
use EzSystems\EzPlatformAdminUi\Form\DataMapper\SectionUpdateMapper;
use PHPUnit\Framework\TestCase;

class SelectionUpdateMapperTest extends TestCase
{
    /** @var \EzSystems\EzPlatformAdminUi\Form\DataMapper\SectionUpdateMapper */
    private $mapper;

    protected function setUp(): void
    {
        $this->mapper = new SectionUpdateMapper();
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
        $this->expectExceptionMessage('Argument \'value\' is invalid: must be an instance of ' . SectionUpdateStruct::class);

        $this->mapper->map(new LocationCreateStruct());
    }

    public function testReverseMapWithWrongInstance()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument \'data\' is invalid: must be an instance of ' . SectionUpdateData::class);

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
     * @return \eZ\Publish\API\Repository\Values\Content\SectionUpdateStruct
     */
    private function createStruct(array $properties): SectionUpdateStruct
    {
        return new SectionUpdateStruct($properties);
    }

    /**
     * @param array $properties
     *
     * @return \EzSystems\EzPlatformAdminUi\Form\Data\Section\SectionUpdateData
     */
    private function createData(array $properties): SectionUpdateData
    {
        return new SectionUpdateData(new Section($properties));
    }
}
