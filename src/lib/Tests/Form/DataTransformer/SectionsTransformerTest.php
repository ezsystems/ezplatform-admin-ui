<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tests\Form\DataTransformer;

use eZ\Publish\API\Repository\SectionService;
use eZ\Publish\API\Repository\Values\Content\Section as APISection;
use EzSystems\EzPlatformAdminUi\Form\DataTransformer\SectionsTransformer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Exception\TransformationFailedException;

class SectionsTransformerTest extends TestCase
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
        $transformer = new SectionsTransformer($service);

        $result = $transformer->transform($value);

        $this->assertEquals($expected, $result);
    }

    public function testReverseTransformWithIds()
    {
        $service = $this->createMock(SectionService::class);
        $service->expects(self::exactly(2))
            ->method('loadSection')
            ->willReturnMap([
                [123456, new APISection(['id' => 123456])],
                [456789, new APISection(['id' => 456789])],
            ]);

        $transformer = new SectionsTransformer($service);
        $result = $transformer->reverseTransform('123456,456789');

        $this->assertEquals([new APISection(['id' => 123456]), new APISection(['id' => 456789])], $result);
    }

    /**
     * @dataProvider reverseTransformWithEmptyDataProvider
     *
     * @param $value
     */
    public function testReverseTransformWithEmpty($value)
    {
        $service = $this->createMock(SectionService::class);
        $service->expects(self::never())
            ->method('loadSection');

        $transformer = new SectionsTransformer($service);
        $result = $transformer->reverseTransform($value);

        $this->assertNull($result);
    }

    /**
     * @dataProvider reverseTransformWithInvalidInputDataProvider
     *
     * @param $value
     */
    public function testReverseTransformWithInvalidInput($value)
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Expected a string.');

        $service = $this->createMock(SectionService::class);
        $transformer = new SectionsTransformer($service);

        $transformer->reverseTransform($value);
    }

    /**
     * @return array
     */
    public function transformDataProvider(): array
    {
        $location_1 = new APISection(['id' => 123456]);
        $location_2 = new APISection(['id' => 456789]);

        return [
            'with_array_of_ids' => [[$location_1, $location_2], '123456,456789'],
            'with_array_of_id' => [[$location_1], '123456'],
            'null' => [null, null],
            'string' => ['string', null],
            'empty_array' => [[], null],
        ];
    }

    /**
     * @return array
     */
    public function reverseTransformWithInvalidInputDataProvider(): array
    {
        return [
            'integer' => [123456],
            'bool' => [true],
            'float' => [12.34],
            'array' => [['element']],
            'object' => [new \stdClass()],
        ];
    }

    /**
     * @return array
     */
    public function reverseTransformWithEmptyDataProvider(): array
    {
        return [
            'an_empty_string' => [''],
            '0_as_an_integer' => [0],
            '0_as_a_float' => [0.0],
            '0_as_a_string' => ['0'],
            'null' => [null],
            'false' => [false],
            'an_empty_array' => [[]],
        ];
    }
}
