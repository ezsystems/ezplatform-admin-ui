<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tests\Form\DataTransformer;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\Location as APILocation;
use eZ\Publish\Core\Repository\Values\Content\Location;
use EzSystems\EzPlatformAdminUi\Form\DataTransformer\LocationTransformer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Exception\TransformationFailedException;

class LocationTransformerTest extends TestCase
{
    /**
     * @dataProvider transformDataProvider
     *
     * @param $value
     * @param $expected
     */
    public function testTransform($value, $expected)
    {
        $service = $this->createMock(LocationService::class);
        $transformer = new LocationTransformer($service);

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
        $languageService = $this->createMock(LocationService::class);
        $transformer = new LocationTransformer($languageService);

        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Expected a ' . APILocation::class . ' object.');

        $transformer->transform($value);
    }

    public function testReverseTransformWithId()
    {
        $service = $this->createMock(LocationService::class);
        $service->expects(self::once())
            ->method('loadLocation')
            ->with(123456)
            ->willReturn(new Location(['id' => 123456]));

        $transformer = new LocationTransformer($service);

        $result = $transformer->reverseTransform(123456);

        $this->assertEquals(new Location(['id' => 123456]), $result);
    }

    public function testReverseTransformWithNull()
    {
        $service = $this->createMock(LocationService::class);
        $service->expects(self::never())
            ->method('loadLocation');

        $transformer = new LocationTransformer($service);

        $result = $transformer->reverseTransform(null);

        $this->assertNull($result);
    }

    public function testReverseTransformWithNotFoundException()
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Location not found');

        $service = $this->createMock(LocationService::class);
        $service->method('loadLocation')
            ->will($this->throwException(new class('Location not found') extends NotFoundException {
            }));

        $transformer = new LocationTransformer($service);

        $transformer->reverseTransform(654321);
    }

    /**
     * @return array
     */
    public function transformDataProvider(): array
    {
        $location = new Location(['id' => 123456]);

        return [
            'content_info_with_id' => [$location, 123456],
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
}
