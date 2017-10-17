<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tests\Form\DataTransformer;

use EzSystems\EzPlatformAdminUi\Form\DataTransformer\SectionTransformer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Exception\TransformationFailedException;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use Throwable;
use eZ\Publish\API\Repository\SectionService;
use eZ\Publish\API\Repository\Values\Content\Section as APISection;

class SectionTransformerTest extends TestCase
{
    /**
     * @dataProvider transformDataProvider
     * @param $value
     * @param $expected
     */
    public function testTransform($value, $expected)
    {
        $service = $this->createMock(SectionService::class);
        $transformer = new SectionTransformer($service);

        $result = $transformer->transform($value);

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider transformWithInvalidInputDataProvider
     * @param $value
     */
    public function testTransformWithInvalidInput($value)
    {
        $languageService = $this->createMock(SectionService::class);
        $transformer = new SectionTransformer($languageService);

        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Expected a ' . APISection::class . ' object.');
        $transformer->transform($value);
    }

    public function testReverseTransformWithId()
    {
        $service = $this->createMock(SectionService::class);
        $service->expects(self::once())
            ->method('loadSection')
            ->with(123456)
            ->willReturn(new APISection(['id' => 123456]));

        $transformer = new SectionTransformer($service);

        $result = $transformer->reverseTransform(123456);

        $this->assertEquals(new APISection(['id' => 123456]), $result);
    }

    public function testReverseTransformWithNull()
    {
        $service = $this->createMock(SectionService::class);
        $service->expects(self::never())
            ->method('loadSection');

        $transformer = new SectionTransformer($service);

        $result = $transformer->reverseTransform(null);

        $this->assertEquals(null, $result);
    }

    public function testReverseTransformWithNotFoundException()
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Transformation failed. Location not found');

        $service = $this->createMock(SectionService::class);
        $service->method('loadSection')
            ->will($this->throwException(new class() extends NotFoundException {
                public function __construct($message = '', $code = 0, Throwable $previous = null)
                {
                    parent::__construct('Location not found', $code, $previous);
                }
            }));

        $transformer = new SectionTransformer($service);

        $transformer->reverseTransform(654321);
    }

    public function transformDataProvider()
    {
        $transform = new APISection(['id' => 123456]);

        return [
            'with_id' => [$transform, 123456],
            'null' => [null, null],
        ];
    }

    public function transformWithInvalidInputDataProvider()
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
