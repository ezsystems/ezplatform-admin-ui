<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Tests\ParamConverter;

use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\Values\Content\Language;
use EzSystems\EzPlatformAdminUiBundle\ParamConverter\LanguageParamConverter;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LanguageParamConverterTest extends AbstractParamConverterTest
{
    const SUPPORTED_CLASS = Language::class;
    const PARAMETER_NAME = 'language';

    /** @var LanguageParamConverter */
    protected $converter;

    /** @var MockObject */
    protected $serviceMock;

    public function setUp()
    {
        $this->serviceMock = $this->createMock(LanguageService::class);

        $this->converter = new LanguageParamConverter($this->serviceMock);
    }

    public function testApply()
    {
        $languageId = 42;
        $valueObject = $this->createMock(Language::class);

        $this->serviceMock
            ->expects($this->once())
            ->method('loadLanguageById')
            ->with($languageId)
            ->willReturn($valueObject);

        $requestAttributes = [
            LanguageParamConverter::PARAMETER_LANGUAGE_ID => $languageId,
        ];

        $request = new Request([], [], $requestAttributes);
        $config = $this->createConfiguration(self::SUPPORTED_CLASS, self::PARAMETER_NAME);

        $this->converter->apply($request, $config);

        $this->assertInstanceOf(self::SUPPORTED_CLASS, $request->attributes->get(self::PARAMETER_NAME));
    }

    public function testApplyWithWrongAttribute()
    {
        $requestAttributes = [
            LanguageParamConverter::PARAMETER_LANGUAGE_ID => null,
        ];

        $request = new Request([], [], $requestAttributes);
        $config = $this->createConfiguration(self::SUPPORTED_CLASS, self::PARAMETER_NAME);

        $this->assertFalse($this->converter->apply($request, $config));
        $this->assertNull($request->attributes->get(self::PARAMETER_NAME));
    }

    public function testApplyWhenNotFound()
    {
        $languageId = 42;

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage(sprintf('Language %s not found!', $languageId));

        $this->serviceMock
            ->expects($this->once())
            ->method('loadLanguageById')
            ->with($languageId)
            ->willReturn(null);

        $requestAttributes = [
            LanguageParamConverter::PARAMETER_LANGUAGE_ID => $languageId,
        ];

        $request = new Request([], [], $requestAttributes);
        $config = $this->createConfiguration(self::SUPPORTED_CLASS, self::PARAMETER_NAME);

        $this->converter->apply($request, $config);
    }
}
