<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tests\Form\DataTransformer;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\SectionService;
use eZ\Publish\API\Repository\Values\Content\Section as APISection;
use EzSystems\EzPlatformAdminUi\Form\DataTransformer\SectionTransformer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Exception\TransformationFailedException;

class SectionTransformerTest extends TestCase
{
    /**
     * @dataProvider transformDataProvider
     *
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
     *
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

        $this->assertNull($result);
    }

    public function testReverseTransformWithNotFoundException()
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Section not found');

        $service = $this->createMock(SectionService::class);
        $service->method('loadSection')
            ->will($this->throwException(new class('Section not found') extends NotFoundException {
            }));

        $transformer = new SectionTransformer($service);

        $transformer->reverseTransform(654321);
    }

    public function testReverseTransformWithNonNumericString(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Expected a numeric string.');

        $service = $this->createMock(SectionService::class);
        $service->expects(self::never())->method('loadSection');

        $transformer = new SectionTransformer($service);
        $transformer->reverseTransform('XYZ');
    }

    /**
     * @return array
     */
    public function transformDataProvider(): array
    {
        $transform = new APISection(['id' => 123456]);

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
}
