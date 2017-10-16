<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tests\Form\DataTransformer;

use EzSystems\EzPlatformAdminUi\Form\DataTransformer\LanguageTransformer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Exception\TransformationFailedException;
use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use Throwable;

class LanguageTransformerTest extends TestCase
{
    /**
     * @dataProvider transformDataProvider
     * @param $value
     * @param $expected
     */
    public function testTransform($value, $expected)
    {
        $languageService = $this->createMock(LanguageService::class);
        $transformer = new LanguageTransformer($languageService);

        $result = $transformer->transform($value);

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider wrongValueDataProvider
     * @param $value
     */
    public function testTransformWithWrongValue($value)
    {
        $languageService = $this->createMock(LanguageService::class);
        $transformer = new LanguageTransformer($languageService);

        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Expected a ' . Language::class . ' object.');
        $transformer->transform($value);
    }

    public function testReverseTransformWithId()
    {
        $languageService = $this->createMock(LanguageService::class);
        $languageService->expects(self::once())
            ->method('loadLanguageById')
            ->with(123456)
            ->willReturn(new Language(['id' => 123456]));

        $transformer = new LanguageTransformer($languageService);

        $result = $transformer->reverseTransform(123456);

        $this->assertEquals(new Language(['id' => 123456]), $result);
    }

    public function testReverseTransformWithNull()
    {
        $languageService = $this->createMock(LanguageService::class);
        $languageService->expects(self::never())
            ->method('loadLanguageById');

        $transformer = new LanguageTransformer($languageService);

        $result = $transformer->reverseTransform(null);

        $this->assertEquals(null, $result);
    }

    public function testReverseTransformWithNotFoundException()
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Transformation failed. Language not found');

        $languageService = $this->createMock(LanguageService::class);
        $languageService->method('loadLanguageById')
            ->will($this->throwException(new class() extends NotFoundException {
                public function __construct($message = '', $code = 0, Throwable $previous = null)
                {
                    parent::__construct('Language not found', $code, $previous);
                }
            }));

        $transformer = new LanguageTransformer($languageService);

        $transformer->reverseTransform(654321);
    }

    public function transformDataProvider()
    {
        $language = new Language(['id' => 123456]);

        return [
            'content_info_with_id' => [$language, 123456],
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
