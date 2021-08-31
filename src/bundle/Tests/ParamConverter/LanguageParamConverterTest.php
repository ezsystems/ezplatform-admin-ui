<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Tests\ParamConverter;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\Values\Content\Language;
use EzSystems\EzPlatformAdminUiBundle\ParamConverter\LanguageParamConverter;
use stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LanguageParamConverterTest extends AbstractParamConverterTest
{
    const SUPPORTED_CLASS = Language::class;
    const PARAMETER_NAME = 'language';

    /** @var \EzSystems\EzPlatformAdminUiBundle\ParamConverter\LanguageParamConverter */
    protected $converter;

    /** @var \eZ\Publish\API\Repository\LanguageService|\PHPUnit\Framework\MockObject\MockObject */
    protected $serviceMock;

    protected function setUp(): void
    {
        $this->serviceMock = $this->createMock(LanguageService::class);
        $this->converter = new LanguageParamConverter($this->serviceMock);
    }

    /**
     * @covers \EzSystems\EzPlatformAdminUiBundle\ParamConverter\LanguageParamConverter::apply
     *
     * @dataProvider dataProvider
     *
     * @param mixed $languageId The language identifier fetched from the request
     * @param int $languageIdToLoad The language identifier used to load the language
     */
    public function testApplyForLanguageId($languageId, int $languageIdToLoad)
    {
        $valueObject = $this->createMock(Language::class);

        $this->serviceMock
            ->expects($this->once())
            ->method('loadLanguageById')
            ->with($languageIdToLoad)
            ->willReturn($valueObject);

        $requestAttributes = [
            LanguageParamConverter::PARAMETER_LANGUAGE_ID => $languageId,
        ];

        $request = new Request([], [], $requestAttributes);
        $config = $this->createConfiguration(self::SUPPORTED_CLASS, self::PARAMETER_NAME);

        $this->assertTrue($this->converter->apply($request, $config));
        $this->assertInstanceOf(self::SUPPORTED_CLASS, $request->attributes->get(self::PARAMETER_NAME));
    }

    /**
     * @covers \EzSystems\EzPlatformAdminUiBundle\ParamConverter\LanguageParamConverter::apply
     */
    public function testApplyForLanguageCode()
    {
        $languageCode = 'eng-GB';
        $valueObject = $this->createMock(Language::class);

        $this->serviceMock
            ->expects($this->once())
            ->method('loadLanguage')
            ->with($languageCode)
            ->willReturn($valueObject);

        $request = new Request([], [], [
            LanguageParamConverter::PARAMETER_LANGUAGE_CODE => $languageCode,
        ]);

        $config = $this->createConfiguration(self::SUPPORTED_CLASS, self::PARAMETER_NAME);

        $this->converter->apply($request, $config);

        $this->assertInstanceOf(self::SUPPORTED_CLASS, $request->attributes->get(self::PARAMETER_NAME));
    }

    /**
     * @covers \EzSystems\EzPlatformAdminUiBundle\ParamConverter\LanguageParamConverter::apply
     * @dataProvider dataProviderForApplyWithWrongAttribute
     */
    public function testApplyWithWrongAttribute(array $attributes)
    {
        $request = new Request([], [], $attributes);
        $config = $this->createConfiguration(self::SUPPORTED_CLASS, self::PARAMETER_NAME);

        $this->assertFalse($this->converter->apply($request, $config));
        $this->assertNull($request->attributes->get(self::PARAMETER_NAME));
    }

    /**
     * @covers \EzSystems\EzPlatformAdminUiBundle\ParamConverter\LanguageParamConverter::apply
     */
    public function testApplyWithNonExistingLanguageId()
    {
        $languageId = 42;

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage(sprintf('Language %s not found.', $languageId));

        $this->serviceMock
            ->expects($this->once())
            ->method('loadLanguageById')
            ->with($languageId)
            ->willThrowException($this->createMock(NotFoundException::class));

        $requestAttributes = [
            LanguageParamConverter::PARAMETER_LANGUAGE_ID => $languageId,
        ];

        $request = new Request([], [], $requestAttributes);
        $config = $this->createConfiguration(self::SUPPORTED_CLASS, self::PARAMETER_NAME);

        $this->converter->apply($request, $config);
    }

    /**
     * @covers \EzSystems\EzPlatformAdminUiBundle\ParamConverter\LanguageParamConverter::apply
     */
    public function testApplyWithNonExistingLanguageCode()
    {
        $languageCode = 'eng-Gb';

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage(sprintf('Language %s not found.', $languageCode));

        $this->serviceMock
            ->expects($this->once())
            ->method('loadLanguage')
            ->with($languageCode)
            ->willThrowException($this->createMock(NotFoundException::class));

        $requestAttributes = [
            LanguageParamConverter::PARAMETER_LANGUAGE_CODE => $languageCode,
        ];

        $request = new Request([], [], $requestAttributes);
        $config = $this->createConfiguration(self::SUPPORTED_CLASS, self::PARAMETER_NAME);

        $this->converter->apply($request, $config);
    }

    /**
     * @covers \EzSystems\EzPlatformAdminUiBundle\ParamConverter\LanguageParamConverter::supports
     * @dataProvider dataProviderForSupport
     */
    public function testSupport(string $class, bool $expected)
    {
        $this->assertEquals($expected, $this->converter->supports($this->createConfiguration($class)));
    }

    public function dataProviderForSupport(): array
    {
        return [
            [self::SUPPORTED_CLASS, true],
            [stdClass::class, false],
        ];
    }

    public function dataProviderForApplyWithWrongAttribute(): array
    {
        return [
            [
                [LanguageParamConverter::PARAMETER_LANGUAGE_ID => null],
            ],
            [
                [LanguageParamConverter::PARAMETER_LANGUAGE_CODE => null],
            ],
            [
                [],
            ],
        ];
    }

    public function dataProvider(): array
    {
        return [
            'integer' => [42, 42],
            'number_as_string' => ['42', 42],
            'string' => ['42k', 42],
        ];
    }
}
