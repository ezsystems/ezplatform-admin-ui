<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tests\Form\DataTransformer;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use EzSystems\EzPlatformAdminUi\Form\DataTransformer\VersionInfoTransformer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Exception\TransformationFailedException;

final class VersionInfoTransformerTest extends TestCase
{
    private const EXAMPLE_CONTENT_ID = 123456;
    private const EXAMPLE_VERSION_NO = 7;

    /** @var \eZ\Publish\API\Repository\ContentService|\PHPUnit\Framework\MockObject\MockObject */
    private $contentService;

    /** @var \EzSystems\EzPlatformAdminUi\Form\DataTransformer\VersionInfoTransformer */
    private $transformer;

    protected function setUp(): void
    {
        $this->contentService = $this->createMock(ContentService::class);
        $this->transformer = new VersionInfoTransformer($this->contentService);
    }

    /**
     * @dataProvider dataProviderForTransformWithValidInput
     */
    public function testTransformWithValidInput(?VersionInfo $value, ?array $expected): void
    {
        $this->assertEquals(
            $expected,
            $this->transformer->transform($value)
        );
    }

    public function dataProviderForTransformWithValidInput(): array
    {
        $contentInfo = new ContentInfo([
            'id' => self::EXAMPLE_CONTENT_ID,
        ]);

        $versionInfo = $this->createVersionInfoMock($contentInfo, self::EXAMPLE_VERSION_NO);

        return [
            [null, null],
            [
                $versionInfo,
                [
                    'content_info' => $contentInfo,
                    'version_no' => self::EXAMPLE_VERSION_NO,
                ],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForTransformWithInvalidInput
     */
    public function testTransformWithInvalidInput($value): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Value cannot be transformed because the passed value is not a VersionInfo object');

        $this->transformer->transform($value);
    }

    public function dataProviderForTransformWithInvalidInput(): array
    {
        $object = new class() {
        };

        return [
            'string' => ['string'],
            'integer' => [123456],
            'bool' => [true],
            'float' => [12.34],
            'array' => [[]],
            'object' => [$object],
        ];
    }

    /**
     * @dataProvider dataProviderForReverseTransformWithValidInput
     */
    public function testReverseTransformWithValidInput(?array $value, ?VersionInfo $expected): void
    {
        if ($expected !== null) {
            $this->contentService
                ->expects($this->once())
                ->method('loadVersionInfo')
                ->with(
                    $this->equalTo($value['content_info']),
                    $this->logicalAnd(
                        $this->equalTo($value['version_no']),
                        // Make sure value is casted to int
                        $this->isType('int')
                    )
                )
                ->willReturn($expected);
        }

        $this->assertEquals(
            $expected,
            $this->transformer->reverseTransform($value)
        );
    }

    public function dataProviderForReverseTransformWithValidInput(): array
    {
        $contentInfo = new ContentInfo([
            'id' => self::EXAMPLE_CONTENT_ID,
        ]);

        $versionInfo = $this->createVersionInfoMock($contentInfo, self::EXAMPLE_VERSION_NO);

        return [
            'null' => [null, null],
            'empty' => [
                [
                    'content_info' => null,
                    'version_no' => null,
                ],
                null,
            ],
            'non_empty' => [
                [
                    'content_info' => $contentInfo,
                    'version_no' => self::EXAMPLE_VERSION_NO,
                ],
                $versionInfo,
            ],
            'non_empty_with_version_cast' => [
                [
                    'content_info' => $contentInfo,
                    'version_no' => (string)self::EXAMPLE_VERSION_NO,
                ],
                $versionInfo,
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForReverseTransformWithInvalidInput
     */
    public function testReverseTransformWithInvalidInput($value): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage("Invalid data. Value array is missing 'content_info' and/or 'version_no' keys");

        $this->transformer->reverseTransform($value);
    }

    public function dataProviderForReverseTransformWithInvalidInput(): array
    {
        return [
            'empty_array' => [
                [],
            ],
        ];
    }

    public function testReverseTransformForNonExistingVersionInfo(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('VersionInfo not found');

        $contentInfo = new ContentInfo([
            'id' => self::EXAMPLE_CONTENT_ID,
        ]);

        $value = [
            'content_info' => $contentInfo,
            'version_no' => self::EXAMPLE_VERSION_NO,
        ];

        $exception = new class('VersionInfo not found') extends NotFoundException {
        };

        $this->contentService
            ->method('loadVersionInfo')
            ->with($contentInfo, self::EXAMPLE_VERSION_NO)
            ->willThrowException($exception);

        $this->transformer->reverseTransform($value);
    }

    public function testReverseTransformForUnauthorizedVersionInfo(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Unauthorized VersionInfo');

        $contentInfo = new ContentInfo([
            'id' => self::EXAMPLE_CONTENT_ID,
        ]);

        $value = [
            'content_info' => $contentInfo,
            'version_no' => self::EXAMPLE_VERSION_NO,
        ];

        $exception = new class('Unauthorized VersionInfo') extends UnauthorizedException {
        };

        $this->contentService
            ->method('loadVersionInfo')
            ->with($contentInfo, self::EXAMPLE_VERSION_NO)
            ->willThrowException($exception);

        $this->transformer->reverseTransform($value);
    }

    private function createVersionInfoMock(ContentInfo $contentInfo, int $versionNo): VersionInfo
    {
        $versionInfo = $this->createMock(VersionInfo::class);
        $versionInfo
            ->method('__get')
            ->willReturnMap([
                ['versionNo', $versionNo],
            ]);
        $versionInfo->method('getContentInfo')->willReturn($contentInfo);

        return $versionInfo;
    }
}
