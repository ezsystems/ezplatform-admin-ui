<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tests\Form\DataTransformer;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Exception\TransformationFailedException;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use Throwable;
use EzSystems\EzPlatformAdminUi\Form\DataTransformer\LocationTransformer;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\Core\Repository\Values\Content\Location;

class LocationTransformerTest extends TestCase
{
    /**
     * @dataProvider transformDataProvider
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
     * @dataProvider wrongValueDataProvider
     * @param $value
     */
    public function testTransformWithWrongValue($value)
    {
        $languageService = $this->createMock(LocationService::class);
        $transformer = new LocationTransformer($languageService);

        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Expected a ' . Location::class . ' object.');
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

        $this->assertEquals(null, $result);
    }

    public function testReverseTransformWithNotFoundException()
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Transformation failed. Location not found');

        $service = $this->createMock(LocationService::class);
        $service->method('loadLocation')
            ->will($this->throwException(new class() extends NotFoundException {
                public function __construct($message = '', $code = 0, Throwable $previous = null)
                {
                    parent::__construct('Location not found', $code, $previous);
                }
            }));

        $transformer = new LocationTransformer($service);

        $transformer->reverseTransform(654321);
    }

    public function transformDataProvider()
    {
        $location = new Location(['id' => 123456]);

        return [
            'content_info_with_id' => [$location, 123456],
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
