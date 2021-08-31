<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Tests\Form\DataMapper;

use eZ\Publish\API\Repository\Values\Content\LocationCreateStruct;
use eZ\Publish\Core\Repository\Values\User\RoleCreateStruct;
use EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException;
use EzSystems\EzPlatformAdminUi\Form\Data\Language\LanguageCreateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Role\RoleCreateData;
use EzSystems\EzPlatformAdminUi\Form\DataMapper\RoleCreateMapper;
use PHPUnit\Framework\TestCase;

class RoleCreateMapperTest extends TestCase
{
    /** @var \EzSystems\EzPlatformAdminUi\Form\DataMapper\RoleCreateMapper */
    private $mapper;

    protected function setUp(): void
    {
        $this->mapper = new RoleCreateMapper();
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
        $this->expectExceptionMessage('Argument \'value\' is invalid: must be an instance of ' . RoleCreateStruct::class);

        $this->mapper->map(new LocationCreateStruct());
    }

    public function testReverseMapWithWrongInstance()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument \'data\' is invalid: must be an instance of ' . RoleCreateData::class);

        $this->mapper->reverseMap(new LanguageCreateData());
    }

    public function dataProvider(): array
    {
        return [
            'simple' => [['identifier' => 'hash']],
        ];
    }

    /**
     * @param array $properties
     *
     * @return \eZ\Publish\Core\Repository\Values\User\RoleCreateStruct
     */
    private function createStruct(array $properties): RoleCreateStruct
    {
        return new RoleCreateStruct($properties);
    }

    /**
     * @param array $properties
     *
     * @return \EzSystems\EzPlatformAdminUi\Form\Data\Role\RoleCreateData
     */
    private function createData(array $properties): RoleCreateData
    {
        return (new RoleCreateData())
            ->setIdentifier($properties['identifier']);
    }
}
