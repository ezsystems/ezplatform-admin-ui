<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tests\Form\DataTransformer;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\RoleService;
use eZ\Publish\API\Repository\Values\User\Role as APIRole;
use eZ\Publish\Core\Repository\Values\User\Role;
use EzSystems\EzPlatformAdminUi\Form\DataTransformer\RoleTransformer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Exception\TransformationFailedException;

class RoleTransformerTest extends TestCase
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
        $transformer = new RoleTransformer($service);

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
        $transformer = new RoleTransformer($roleService);

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

        $this->assertNull($result);
    }

    /**
     * @dataProvider reverseTransformWithInvalidInputDataProvider
     */
    public function testReverseTransformWithInvalidInput($value)
    {
        $roleService = $this->createMock(RoleService::class);
        $transformer = new RoleTransformer($roleService);

        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Expected a numeric string.');

        $transformer->reverseTransform($value);
    }

    public function testReverseTransformWithNotFoundException()
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Location not found');

        $service = $this->createMock(RoleService::class);
        $service->method('loadRole')
            ->will($this->throwException(new class('Location not found') extends NotFoundException {
            }));

        $transformer = new RoleTransformer($service);

        $transformer->reverseTransform(654321);
    }

    /**
     * @return array
     */
    public function transformDataProvider(): array
    {
        $transform = new Role(['id' => 123456]);

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
