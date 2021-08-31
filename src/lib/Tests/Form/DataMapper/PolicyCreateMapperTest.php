<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Tests\Form\DataMapper;

use eZ\Publish\API\Repository\Values\Content\LocationCreateStruct;
use eZ\Publish\Core\Repository\Values\User\PolicyCreateStruct;
use EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException;
use EzSystems\EzPlatformAdminUi\Form\Data\Policy\PolicyCreateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Policy\PolicyUpdateData;
use EzSystems\EzPlatformAdminUi\Form\DataMapper\PolicyCreateMapper;
use PHPUnit\Framework\TestCase;

class PolicyCreateMapperTest extends TestCase
{
    /** @var \EzSystems\EzPlatformAdminUi\Form\DataMapper\PolicyCreateMapper */
    private $mapper;

    protected function setUp(): void
    {
        $this->mapper = new PolicyCreateMapper();
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
        $this->expectExceptionMessage('Argument \'value\' is invalid: must be an instance of ' . PolicyCreateStruct::class);

        $this->mapper->map(new LocationCreateStruct());
    }

    public function testReverseMapWithWrongInstance()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument \'data\' is invalid: must be an instance of ' . PolicyCreateData::class);

        $this->mapper->reverseMap(new PolicyUpdateData());
    }

    public function dataProvider(): array
    {
        return [
            'simple' => [['module' => 'module', 'function' => 'function']],
        ];
    }

    /**
     * @param array $properties
     *
     * @return \eZ\Publish\Core\Repository\Values\User\PolicyCreateStruct
     */
    private function createStruct(array $properties): PolicyCreateStruct
    {
        return new PolicyCreateStruct($properties);
    }

    /**
     * @param array $properties
     *
     * @return \EzSystems\EzPlatformAdminUi\Form\Data\Policy\PolicyCreateData
     */
    private function createData(array $properties): PolicyCreateData
    {
        return (new PolicyCreateData())
            ->setModule($properties['module'])
            ->setFunction($properties['function']);
    }
}
