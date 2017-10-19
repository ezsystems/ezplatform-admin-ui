<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Tests\Form\DataMapper;

use eZ\Publish\API\Repository\Values\User\Limitation\ContentTypeLimitation;
use eZ\Publish\Core\Repository\Values\User\PolicyUpdateStruct;
use eZ\Publish\API\Repository\Values\Content\LocationCreateStruct;
use EzSystems\EzPlatformAdminUi\Form\Data\Policy\PolicyUpdateData;
use EzSystems\EzPlatformAdminUi\Form\DataMapper\PolicyUpdateMapper;
use PHPUnit\Framework\TestCase;
use EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException;

class PolicyUpdateMapperTest extends TestCase
{
    /**
     * @var PolicyUpdateMapper
     */
    private $mapper;

    public function setUp()
    {
        /* TODO - test skipped, because tested class need to be improved */
        $this->markTestSkipped();
        $this->mapper = new PolicyUpdateMapper();
    }

    public function tearDown()
    {
        unset($this->mapper);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testMap(array $properties)
    {
        $data = $this->mapper->map($this->createStruct($properties));

        $this->assertEquals($this->createData($properties), $data);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testReverseMap(array $properties)
    {
        $struct = $this->mapper->reverseMap($this->createData($properties));

        $this->assertEquals($this->createStruct($properties), $struct);
    }

    public function testMapWithWrongInstance()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument \'value\' is invalid: must be instance of ' . PolicyUpdateStruct::class);
        $this->mapper->map(new LocationCreateStruct());
    }

    public function dataProvider()
    {
        return [
            'simple' => [['limitation' => new ContentTypeLimitation()]],
        ];
    }

    private function createStruct(array $properties): PolicyUpdateStruct
    {
        $struct = new PolicyUpdateStruct();
        $struct->addLimitation($properties['limitation']);

        return $struct;
    }

    private function createData(array $properties): PolicyUpdateData
    {
        return new PolicyUpdateData(['module' => $properties['module'], 'function' => $properties['function']]);
    }
}
