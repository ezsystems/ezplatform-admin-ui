<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tests\Form\DataTransformer;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup as APIContentTypeGroup;
use eZ\Publish\Core\Repository\Values\ContentType\ContentTypeGroup;
use EzSystems\EzPlatformAdminUi\Form\DataTransformer\ContentTypeGroupTransformer;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Form\Exception\TransformationFailedException;

final class ContentTypeGroupTransformerTest extends TestCase
{
    private const EXAMPLE_CONTENT_TYPE_GROUP_ID = 1;

    /** @var \eZ\Publish\API\Repository\ContentTypeService|\PHPUnit\Framework\MockObject\MockObject */
    private $contentService;

    /** @var \EzSystems\EzPlatformAdminUi\Form\DataTransformer\ContentTypeGroupTransformer */
    private $transformer;

    protected function setUp(): void
    {
        $this->contentService = $this->createMock(ContentTypeService::class);
        $this->transformer = new ContentTypeGroupTransformer($this->contentService);
    }

    /**
     * @dataProvider dataProviderForTransformWithValidInput
     */
    public function testTransformWithValidInput(?APIContentTypeGroup $value, ?int $expected): void
    {
        $this->assertEquals($expected, $this->transformer->transform($value));
    }

    public function dataProviderForTransformWithValidInput(): array
    {
        $contentTypeGroup = new ContentTypeGroup([
            'id' => self::EXAMPLE_CONTENT_TYPE_GROUP_ID,
        ]);

        return [
            'null' => [null, null],
            'content_type_group_with_id' => [$contentTypeGroup, self::EXAMPLE_CONTENT_TYPE_GROUP_ID],
        ];
    }

    /**
     * @dataProvider dataProviderForTransformWithInvalidInput
     */
    public function testTransformWithInvalidInput($value): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Expected a ' . APIContentTypeGroup::class . ' object.');

        $this->transformer->transform($value);
    }

    public function dataProviderForTransformWithInvalidInput(): array
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
     * @dataProvider dataProviderForReverseTransformWithValidInput
     */
    public function testReverseTransformWithValidInput($value, ?APIContentTypeGroup $expected): void
    {
        if ($expected !== null) {
            $this->contentService
                ->method('loadContentTypeGroup')
                ->with($expected->id)
                ->willReturn($expected);
        }

        $this->assertEquals(
            $expected,
            $this->transformer->reverseTransform($value)
        );
    }

    public function dataProviderForReverseTransformWithValidInput(): array
    {
        $contentTypeGroup = new ContentTypeGroup([
            'id' => self::EXAMPLE_CONTENT_TYPE_GROUP_ID,
        ]);

        return [
            'integer' => [
                self::EXAMPLE_CONTENT_TYPE_GROUP_ID,
                $contentTypeGroup,
            ],
            'string' => [
                (string)self::EXAMPLE_CONTENT_TYPE_GROUP_ID,
                $contentTypeGroup,
            ],
            'null' => [null, null],
        ];
    }

    /**
     * @dataProvider dataProviderForReverseTransformWithInvalidInput
     */
    public function testReverseTransformWithInvalidInput($value): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Expected a numeric string.');

        $this->transformer->reverseTransform($value);
    }

    public function dataProviderForReverseTransformWithInvalidInput(): array
    {
        return [
            'string' => ['string'],
            'bool' => [true],
            'array' => [['element']],
            'object' => [new stdClass()],
        ];
    }

    public function testReverseTransformWithNotFoundException(): void
    {
        $expectedExceptionMessage = APIContentTypeGroup::class . ' not found';

        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $exception = new class($expectedExceptionMessage) extends NotFoundException {
        };

        $this->contentService
            ->method('loadContentTypeGroup')
            ->with(self::EXAMPLE_CONTENT_TYPE_GROUP_ID)
            ->willThrowException($exception);

        $this->transformer->reverseTransform(self::EXAMPLE_CONTENT_TYPE_GROUP_ID);
    }
}
