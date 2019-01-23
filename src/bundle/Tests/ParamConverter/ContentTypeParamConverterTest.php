<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Tests\ParamConverter;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface;
use EzSystems\EzPlatformAdminUiBundle\ParamConverter\ContentTypeParamConverter;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ContentTypeParamConverterTest extends AbstractParamConverterTest
{
    const SUPPORTED_CLASS = ContentType::class;
    const PARAMETER_NAME = 'contentType';

    /** @var ContentTypeParamConverter */
    protected $converter;

    /** @var MockObject */
    protected $serviceMock;

    public function setUp()
    {
        $this->serviceMock = $this->createMock(ContentTypeService::class);

        $userLanguagePreferenceProvider = $this->createMock(UserLanguagePreferenceProviderInterface::class);
        $this->converter = new ContentTypeParamConverter($this->serviceMock, $userLanguagePreferenceProvider);
    }

    public function testApplyId()
    {
        $contentTypeId = 42;
        $valueObject = $this->createMock(ContentType::class);

        $this->serviceMock
            ->expects($this->once())
            ->method('loadContentType')
            ->with($contentTypeId)
            ->willReturn($valueObject);

        $requestAttributes = [
            ContentTypeParamConverter::PARAMETER_CONTENT_TYPE_ID => $contentTypeId,
        ];

        $request = new Request([], [], $requestAttributes);
        $config = $this->createConfiguration(self::SUPPORTED_CLASS, self::PARAMETER_NAME);

        $this->converter->apply($request, $config);

        $this->assertInstanceOf(self::SUPPORTED_CLASS, $request->attributes->get(self::PARAMETER_NAME));
    }

    public function testApplyIdWithWrongValue()
    {
        $requestAttributes = [
            ContentTypeParamConverter::PARAMETER_CONTENT_TYPE_ID => null,
        ];

        $request = new Request([], [], $requestAttributes);
        $config = $this->createConfiguration(self::SUPPORTED_CLASS, self::PARAMETER_NAME);

        $this->assertFalse($this->converter->apply($request, $config));
        $this->assertNull($request->attributes->get(self::PARAMETER_NAME));
    }

    public function testApplyIdWhenNotFound()
    {
        $contentTypeId = 42;

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage(sprintf('ContentType %s not found!', $contentTypeId));

        $this->serviceMock
            ->expects($this->once())
            ->method('loadContentType')
            ->with($contentTypeId)
            ->willReturn(null);

        $requestAttributes = [
            ContentTypeParamConverter::PARAMETER_CONTENT_TYPE_ID => $contentTypeId,
        ];

        $request = new Request([], [], $requestAttributes);
        $config = $this->createConfiguration(self::SUPPORTED_CLASS, self::PARAMETER_NAME);

        $this->converter->apply($request, $config);
    }

    public function testApplyIdentifier()
    {
        $contentTypeIdentifier = 'test_identifier';
        $valueObject = $this->createMock(ContentType::class);

        $this->serviceMock
            ->expects($this->once())
            ->method('loadContentTypeByIdentifier')
            ->with($contentTypeIdentifier)
            ->willReturn($valueObject);

        $requestAttributes = [
            ContentTypeParamConverter::PARAMETER_CONTENT_TYPE_IDENTIFIER => $contentTypeIdentifier,
        ];

        $request = new Request([], [], $requestAttributes);
        $config = $this->createConfiguration(self::SUPPORTED_CLASS, self::PARAMETER_NAME);

        $this->converter->apply($request, $config);

        $this->assertInstanceOf(self::SUPPORTED_CLASS, $request->attributes->get(self::PARAMETER_NAME));
    }

    public function testApplyIdentifierWithWrongValue()
    {
        $requestAttributes = [
            ContentTypeParamConverter::PARAMETER_CONTENT_TYPE_IDENTIFIER => null,
        ];

        $request = new Request([], [], $requestAttributes);
        $config = $this->createConfiguration(self::SUPPORTED_CLASS, self::PARAMETER_NAME);

        $this->assertFalse($this->converter->apply($request, $config));
        $this->assertNull($request->attributes->get(self::PARAMETER_NAME));
    }

    public function testApplyIdentifierWhenNotFound()
    {
        $contentTypeIdentifier = 'test_identifier';

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage(sprintf('ContentType %s not found!', $contentTypeIdentifier));

        $this->serviceMock
            ->expects($this->once())
            ->method('loadContentTypeByIdentifier')
            ->with($contentTypeIdentifier)
            ->willReturn(null);

        $requestAttributes = [
            ContentTypeParamConverter::PARAMETER_CONTENT_TYPE_IDENTIFIER => $contentTypeIdentifier,
        ];

        $request = new Request([], [], $requestAttributes);
        $config = $this->createConfiguration(self::SUPPORTED_CLASS, self::PARAMETER_NAME);

        $this->converter->apply($request, $config);
    }
}
