<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tests\Form\DataTransformer;

use eZ\Publish\API\Repository\ContentService;
use PHPUnit\Framework\TestCase;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use EzSystems\EzPlatformAdminUi\Form\DataTransformer\ContentInfoTransformer;
use Symfony\Component\Form\Exception\TransformationFailedException;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use PHPUnit\Framework\MockObject\MockObject;

class ContentInfoTransformerTest extends TestCase
{
    /** @var ContentInfoTransformer */
    private $contentInfoTransformer;

    protected function setUp(): void
    {
        /** @var ContentService|MockObject $contentService */
        $contentService = $this->createMock(ContentService::class);
        $contentService->expects(self::any())
            ->method('loadContentInfo')
            ->with(123456)
            ->willReturn(new ContentInfo(['id' => 123456]));

        $this->contentInfoTransformer = new ContentInfoTransformer($contentService);
    }

    /**
     * @dataProvider transformDataProvider
     *
     * @param $value
     * @param $expected
     */
    public function testTransform($value, $expected)
    {
        $result = $this->contentInfoTransformer->transform($value);

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider transformWithInvalidInputDataProvider
     *
     * @param $value
     */
    public function testTransformWithInvalidInput($value)
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Expected a ' . ContentInfo::class . ' object.');

        $this->contentInfoTransformer->transform($value);
    }

    /**
     * @dataProvider reverseTransformDataProvider
     *
     * @param $value
     * @param $expected
     */
    public function testReverseTransform($value, $expected)
    {
        $result = $this->contentInfoTransformer->reverseTransform($value);

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider reverseTransformWithInvalidInputDataProvider
     *
     * @param $value
     */
    public function testReverseTransformWithInvalidInput($value)
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Expected a numeric string.');

        $this->contentInfoTransformer->reverseTransform($value);
    }

    public function testReverseTransformWithNotFoundException()
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('ContentInfo not found');

        /** @var ContentService|MockObject $service */
        $service = $this->createMock(ContentService::class);
        $service->method('loadContentInfo')
            ->will($this->throwException(new class('ContentInfo not found') extends NotFoundException {
            }));

        $transformer = new ContentInfoTransformer($service);

        $transformer->reverseTransform(654321);
    }

    /**
     * @return array
     */
    public function transformDataProvider(): array
    {
        $contentInfo = new ContentInfo(['id' => 123456]);

        return [
            'content_info_with_id' => [$contentInfo, 123456],
            'null' => [null, null],
        ];
    }

    /**
     * @return array
     */
    public function reverseTransformDataProvider(): array
    {
        $contentInfo = new ContentInfo(['id' => 123456]);

        return [
            'integer' => [123456, $contentInfo],
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

    /**
     * @return array
     */
    public function reverseTransformWithInvalidInputDataProvider(): array
    {
        return [
            'string' => ['string'],
            'bool' => [true],
            'array' => [['element']],
            'object' => [new \stdClass()],
            'content_info' => [new ContentInfo()],
        ];
    }
}
