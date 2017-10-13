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

        $this->contentInfoTransformer = new ContentInfoTransformer($contentService);
    }

    /**
     * @dataProvider dataProvider
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
        $this->expectExceptionMessage('Expected an ' . ContentInfo::class . ' object.');
        $this->contentInfoTransformer->transform($value);
    }

    public function dataProvider()
    {
        $contentInfo = new ContentInfo(['id' => 123456]);

        return [
            'content_info_with_id' => [$contentInfo, 123456],
            'null' => [null, null],
        ];
    }

    public function wrongValueDataProvider()
    {
        return [
            'string' => ['string'],
            'bool' => [true],
            'float' => [(float)12.34],
            'array' => [[]],
            'object' => [new \stdClass()],
        ];
    }
}
