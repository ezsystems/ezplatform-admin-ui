<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tests\Form\DataTransformer;

use EzSystems\EzPlatformAdminUi\Form\DataTransformer\RoleTransformer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Exception\TransformationFailedException;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use Throwable;
use eZ\Publish\API\Repository\RoleService;
use eZ\Publish\Core\Repository\Values\User\Role;
use eZ\Publish\API\Repository\Values\User\Role as APIRole;

class RoleTransformerTest extends TestCase
{
    /**
     * @dataProvider transformDataProvider
     * @param $value
     * @param $expected
     */
    public function testTransform($value, $expected)
    {
        $service = $this->createMock(RoleService::class);
        $transformer = new RoleTransformer($service);

        $result = $transformer->transform($value);

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider wrongValueDataProvider
     * @param $value
     */
    public function testTransformWithWrongValue($value)
    {
        $languageService = $this->createMock(RoleService::class);
        $transformer = new RoleTransformer($languageService);

        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Expected a ' . APIRole::class . ' object.');
        $transformer->transform($value);
    }

    public function testReverseTransformWithId()
    {
        $service = $this->createMock(RoleService::class);
        $service->expects(self::once())
            ->method('loadRole')
            ->with(123456)
            ->willReturn(new Role(['id' => 123456]));

        $transformer = new RoleTransformer($service);

        $result = $transformer->reverseTransform(123456);

        $this->assertEquals(new Role(['id' => 123456]), $result);
    }

    public function testReverseTransformWithNull()
    {
        $service = $this->createMock(RoleService::class);
        $service->expects(self::never())
            ->method('loadRole');

        $transformer = new RoleTransformer($service);

        $result = $transformer->reverseTransform(null);

        $this->assertEquals(null, $result);
    }

    public function testReverseTransformWithNotFoundException()
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Transformation failed. Location not found');

        $service = $this->createMock(RoleService::class);
        $service->method('loadRole')
            ->will($this->throwException(new class() extends NotFoundException {
                public function __construct($message = '', $code = 0, Throwable $previous = null)
                {
                    parent::__construct('Location not found', $code, $previous);
                }
            }));

        $transformer = new RoleTransformer($service);

        $transformer->reverseTransform(654321);
    }

    public function transformDataProvider()
    {
        $transform = new Role(['id' => 123456]);

        return [
            'with_id' => [$transform, 123456],
            'null' => [null, null],
        ];
    }

    public function wrongValueDataProvider()
    {
        return [
            'string' => ['string'],
            'integer' => [123456],
            'bool' => [true],
            'float' => [(float)12.34],
            'array' => [[]],
            'object' => [new \stdClass()],
        ];
    }
}
