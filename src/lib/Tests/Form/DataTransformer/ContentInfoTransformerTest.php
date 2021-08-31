<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tests\Form\DataTransformer;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use EzSystems\EzPlatformAdminUi\Form\DataTransformer\ContentInfoTransformer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Exception\TransformationFailedException;

final class ContentInfoTransformerTest extends TestCase
{
    private const EXAMPLE_CONTENT_ID = 123456;

    /** @var \EzSystems\EzPlatformAdminUi\Form\DataTransformer\ContentInfoTransformer */
    private $contentInfoTransformer;

    protected function setUp(): void
    {
        /** @var \eZ\Publish\API\Repository\ContentService|\PHPUnit\Framework\MockObject\MockObject $contentService */
        $contentService = $this->createMock(ContentService::class);
        $contentService
            ->method('loadContentInfo')
            ->with($this->logicalAnd(
                $this->equalTo(self::EXAMPLE_CONTENT_ID),
                $this->isType('int')
            ))
            ->willReturn(new ContentInfo([
                'id' => self::EXAMPLE_CONTENT_ID,
            ]));

        $this->contentInfoTransformer = new ContentInfoTransformer($contentService);
    }

    /**
     * @param mixed $value
     *
     * @dataProvider transformWithInvalidInputDataProvider
     */
    public function testTransformWithInvalidInput($value): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Expected a ' . ContentInfo::class . ' object.');

        $this->contentInfoTransformer->transform($value);
    }

    /**
     * @dataProvider transformDataProvider
     */
    public function testTransform(?ContentInfo $value, ?int $expected): void
    {
        $result = $this->contentInfoTransformer->transform($value);

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider reverseTransformDataProvider
     */
    public function testReverseTransform($value, ?ContentInfo $expected): void
    {
        $result = $this->contentInfoTransformer->reverseTransform($value);

        $this->assertEquals($expected, $result);
    }

    /**
     * @param mixed $value
     *
     * @dataProvider reverseTransformWithInvalidInputDataProvider
     */
    public function testReverseTransformWithInvalidInput($value): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Expected a numeric string.');

        $this->contentInfoTransformer->reverseTransform($value);
    }

    public function testReverseTransformWithNotFoundException(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('ContentInfo not found');

        /** @var \eZ\Publish\API\Repository\ContentService|\PHPUnit\Framework\MockObject\MockObject $service */
        $service = $this->createMock(ContentService::class);
        $service->method('loadContentInfo')
            ->will($this->throwException(new class('ContentInfo not found') extends NotFoundException {
            }));

        $transformer = new ContentInfoTransformer($service);

        $transformer->reverseTransform(654321);
    }

    public function transformDataProvider(): array
    {
        $contentInfo = new ContentInfo([
            'id' => self::EXAMPLE_CONTENT_ID,
        ]);

        return [
            'content_info_with_id' => [$contentInfo, self::EXAMPLE_CONTENT_ID],
            'null' => [null, null],
        ];
    }

    public function reverseTransformDataProvider(): array
    {
        $contentInfo = new ContentInfo([
            'id' => self::EXAMPLE_CONTENT_ID,
        ]);

        return [
            'integer' => [self::EXAMPLE_CONTENT_ID, $contentInfo],
            'string' => [(string)self::EXAMPLE_CONTENT_ID, $contentInfo],
            'null' => [null, null],
        ];
    }

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
            'array' => [['element']],
            'object' => [new \stdClass()],
            'content_info' => [new ContentInfo()],
        ];
    }
}
