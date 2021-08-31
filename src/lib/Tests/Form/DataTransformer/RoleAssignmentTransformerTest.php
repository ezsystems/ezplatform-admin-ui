<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tests\Form\DataTransformer;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\RoleService;
use eZ\Publish\API\Repository\Values\User\RoleAssignment as APIRoleAsignment;
use eZ\Publish\Core\Repository\Values\User\UserRoleAssignment as RoleAssignment;
use EzSystems\EzPlatformAdminUi\Form\DataTransformer\RoleAssignmentTransformer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Exception\TransformationFailedException;

class RoleAssignmentTransformerTest extends TestCase
{
    /**
     * @dataProvider transformDataProvider
     *
     * @param $value
     * @param $expected
     */
    public function testTransform($value, $expected)
    {
        $service = $this->createMock(RoleService::class);
        $transformer = new RoleAssignmentTransformer($service);

        $result = $transformer->transform($value);

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider transformWithInvalidInputDataProvider
     *
     * @param $value
     */
    public function testTransformWithInvalidInput($value)
    {
        $roleService = $this->createMock(RoleService::class);
        $transformer = new RoleAssignmentTransformer($roleService);

        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Expected a ' . APIRoleAsignment::class . ' object.');

        $transformer->transform($value);
    }

    public function testReverseTransformWithId()
    {
        $service = $this->createMock(RoleService::class);
        $service->expects(self::once())
            ->method('loadRoleAssignment')
            ->with(123456)
            ->willReturn(new RoleAssignment(['id' => 123456]));

        $transformer = new RoleAssignmentTransformer($service);

        $result = $transformer->reverseTransform(123456);

        $this->assertEquals(new RoleAssignment(['id' => 123456]), $result);
    }

    public function testReverseTransformWithNull()
    {
        $service = $this->createMock(RoleService::class);
        $service->expects(self::never())
            ->method('loadRoleAssignment');

        $transformer = new RoleAssignmentTransformer($service);

        $result = $transformer->reverseTransform(null);

        $this->assertNull($result);
    }

    /**
     * @dataProvider reverseTransformWithInvalidInputDataProvider
     */
    public function testReverseTransformWithInvalidInput($value)
    {
        $service = $this->createMock(RoleService::class);

        $transformer = new RoleAssignmentTransformer($service);

        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Expected a numeric string.');

        $transformer->reverseTransform($value);
    }

    public function testReverseTransformWithNotFoundException()
    {
        $service = $this->createMock(RoleService::class);
        $service->method('loadRoleAssignment')
            ->will($this->throwException(new class('Location not found') extends NotFoundException {
            }));

        $transformer = new RoleAssignmentTransformer($service);

        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Location not found');

        $transformer->reverseTransform(654321);
    }

    /**
     * @return array
     */
    public function transformDataProvider(): array
    {
        $transform = new RoleAssignment(['id' => 123456]);

        return [
            'with_id' => [$transform, 123456],
            'null' => [null, null],
        ];
    }

    /**
     * @return array
     */
    public function transformWithInvalidInputDataProvider(): array
    {
        return [
            'string' => ['string'],
            'integer' => [123456],
            'bool' => [true],
            'float' => [12.34],
            'array' => [[]],
            'object' => [new \stdClass()],
        ];
    }

    public function reverseTransformWithInvalidInputDataProvider(): array
    {
        return [
            'string' => ['string'],
            'bool' => [true],
            'float' => [12.34],
            'array' => [[1]],
            'object' => [new \stdClass()],
            'scientific_notation' => ['1337e0'],
            'hexadecimal' => ['0x539'],
        ];
    }
}
