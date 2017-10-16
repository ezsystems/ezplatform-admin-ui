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

class ContentInfoTransformerTest extends TestCase
{
    /**
     * @var ContentInfoTransformer
     */
    private $contentInfoTransformer;

    public function setUp()
    {
        $contentService = $this->createMock(ContentService::class);
        $contentService->expects(self::any())
            ->method('loadContentInfo')
            ->with(123456)
            ->willReturn(new ContentInfo(['id' => 123456]));

        $this->contentInfoTransformer = new ContentInfoTransformer($contentService);
    }

    /**
     * @dataProvider transformDataProvider
     * @param $value
     * @param $expected
     */
    public function testTransform($value, $expected)
    {
        $result = $this->contentInfoTransformer->transform($value);

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider wrongValueDataProvider
     * @param $value
     */
    public function testTransformWithWrongValue($value)
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Expected a ' . ContentInfo::class . ' object.');
        $this->contentInfoTransformer->transform($value);
    }

    /**
     * @dataProvider reverseTransformDataProvider
     * @param $value
     * @param $expected
     */
    public function testReverseTransform($value, $expected)
    {
        $result = $this->contentInfoTransformer->reverseTransform($value);

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider wrongValueForReverseTransformDataProvider
     * @param $value
     */
    public function testReverseTransformWithWrongValue($value)
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Expected an integer.');
        $this->contentInfoTransformer->reverseTransform($value);
    }

    public function transformDataProvider()
    {
        $contentInfo = new ContentInfo(['id' => 123456]);

        return [
            'content_info_with_id' => [$contentInfo, 123456],
            'null' => [null, null],
        ];
    }

    public function reverseTransformDataProvider()
    {
        $contentInfo = new ContentInfo(['id' => 123456]);

        return [
            'integer' => [123456, $contentInfo],
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

    public function wrongValueForReverseTransformDataProvider()
    {
        return [
            'string' => ['string'],
            'bool' => [true],
            'float' => [(float)12.34],
            'array' => [['element']],
            'object' => [new \stdClass()],
            'content_info' => [new ContentInfo()],
        ];
    }
}
