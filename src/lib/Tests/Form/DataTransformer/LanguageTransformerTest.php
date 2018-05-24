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
use PHPUnit_Framework_MockObject_MockObject as MockObject;

class LanguageTransformerTest extends TestCase
{
    /**
     * @dataProvider transformDataProvider
     *
     * @param $value
     * @param $expected
     */
    public function testTransform($value, $expected)
    {
        /** @var LanguageService|MockObject $languageService */
        $languageService = $this->createMock(LanguageService::class);
        $transformer = new LanguageTransformer($languageService);

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
        /** @var LanguageService|MockObject $languageService */
        $languageService = $this->createMock(LanguageService::class);
        $transformer = new LanguageTransformer($languageService);

        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Expected a ' . Language::class . ' object.');

        $transformer->transform($value);
    }

    public function testReverseTransformWithLanguageCode()
    {
        /** @var LanguageService|MockObject $languageService */
        $languageService = $this->createMock(LanguageService::class);
        $languageService->expects(self::once())
            ->method('loadLanguage')
            ->with('eng-GB')
            ->willReturn(new Language(['languageCode' => 'eng-GB']));

        $transformer = new LanguageTransformer($languageService);

        $result = $transformer->reverseTransform('eng-GB');

        $this->assertEquals(new Language(['languageCode' => 'eng-GB']), $result);
    }

    public function testReverseTransformWithNull()
    {
        /** @var LanguageService|MockObject $languageService */
        $languageService = $this->createMock(LanguageService::class);
        $languageService->expects(self::never())
            ->method('loadLanguageById');

        $transformer = new LanguageTransformer($languageService);

        $result = $transformer->reverseTransform(null);

        $this->assertNull($result);
    }

    public function testReverseTransformWithNotFoundException()
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Language not found');

        /** @var LanguageService|MockObject $languageService */
        $languageService = $this->createMock(LanguageService::class);
        $languageService->method('loadLanguage')
            ->will($this->throwException(new class('Language not found') extends NotFoundException {
            }));

        $transformer = new LanguageTransformer($languageService);

        $transformer->reverseTransform('pol-PL');
    }

    /**
     * @return array
     */
    public function transformDataProvider(): array
    {
        $language = new Language(['languageCode' => 'eng-GB']);

        return [
            'content_info_with_language_code' => [$language, 'eng-GB'],
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
