<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Tests\Form\DataMapper;

use eZ\Publish\API\Repository\Values\Content\LocationCreateStruct;
use eZ\Publish\API\Repository\Values\User\Limitation\ContentTypeLimitation;
use eZ\Publish\Core\Repository\Values\User\PolicyUpdateStruct;
use EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException;
use EzSystems\EzPlatformAdminUi\Form\Data\Policy\PolicyUpdateData;
use EzSystems\EzPlatformAdminUi\Form\DataMapper\PolicyUpdateMapper;
use PHPUnit\Framework\TestCase;

class PolicyUpdateMapperTest extends TestCase
{
    /** @var \EzSystems\EzPlatformAdminUi\Form\DataMapper\PolicyUpdateMapper */
    private $mapper;

    protected function setUp(): void
    {
        /* TODO - test skipped, because tested class need to be improved */
        $this->markTestSkipped();
        $this->mapper = new PolicyUpdateMapper();
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
        $this->expectExceptionMessage('Argument \'value\' is invalid: must be an instance of ' . PolicyUpdateStruct::class);

        $this->mapper->map(new LocationCreateStruct());
    }

    public function dataProvider(): array
    {
        return [
            'simple' => [['limitation' => new ContentTypeLimitation()]],
        ];
    }

    /**
     * @param array $properties
     *
     * @return \eZ\Publish\Core\Repository\Values\User\PolicyUpdateStruct
     */
    private function createStruct(array $properties): PolicyUpdateStruct
    {
        $struct = new PolicyUpdateStruct();
        $struct->addLimitation($properties['limitation']);

        return $struct;
    }

    /**
     * @param array $properties
     *
     * @return \EzSystems\EzPlatformAdminUi\Form\Data\Policy\PolicyUpdateData
     */
    private function createData(array $properties): PolicyUpdateData
    {
        return new PolicyUpdateData(['module' => $properties['module'], 'function' => $properties['function']]);
    }
}
