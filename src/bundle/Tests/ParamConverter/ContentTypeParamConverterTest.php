<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Tests\ParamConverter;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use EzSystems\EzPlatformAdminUiBundle\ParamConverter\ContentTypeParamConverter;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ContentTypeParamConverterTest extends AbstractParamConverterTest
{
    const SUPPORTED_CLASS = ContentType::class;
    const PARAMETER_NAME = 'contentType';

    /** @var ContentTypeGroupParamConverter */
    protected $converter;

    /** @var MockObject */
    protected $serviceMock;

    public function setUp()
    {
        $this->serviceMock = $this->createMock(ContentTypeService::class);

        $this->converter = new ContentTypeParamConverter($this->serviceMock);
    }

    public function testApply()
    {
        $contentTypeGroupId = 42;
        $valueObject = $this->createMock(ContentType::class);

        $this->serviceMock
            ->expects($this->once())
            ->method('loadContentType')
            ->with($contentTypeGroupId)
            ->willReturn($valueObject);

        $requestAttributes = [
            ContentTypeParamConverter::PARAMETER_CONTENT_TYPE_ID => $contentTypeGroupId,
        ];

        $request = new Request([], [], $requestAttributes);
        $config = $this->createConfiguration(self::SUPPORTED_CLASS, self::PARAMETER_NAME);

        $this->converter->apply($request, $config);

        $this->assertInstanceOf(self::SUPPORTED_CLASS, $request->attributes->get(self::PARAMETER_NAME));
    }

    public function testApplyWithWrongAttribute()
    {
        $requestAttributes = [
            ContentTypeParamConverter::PARAMETER_CONTENT_TYPE_ID => null,
        ];

        $request = new Request([], [], $requestAttributes);
        $config = $this->createConfiguration(self::SUPPORTED_CLASS, self::PARAMETER_NAME);

        $this->assertFalse($this->converter->apply($request, $config));
        $this->assertNull($request->attributes->get(self::PARAMETER_NAME));
    }

    public function testApplyWhenNotFound()
    {
        $contentTypeGroupId = 42;

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage(sprintf('ContentType %s not found!', $contentTypeGroupId));

        $this->serviceMock
            ->expects($this->once())
            ->method('loadContentType')
            ->with($contentTypeGroupId)
            ->willReturn(null);

        $requestAttributes = [
            ContentTypeParamConverter::PARAMETER_CONTENT_TYPE_ID => $contentTypeGroupId,
        ];

        $request = new Request([], [], $requestAttributes);
        $config = $this->createConfiguration(self::SUPPORTED_CLASS, self::PARAMETER_NAME);

        $this->converter->apply($request, $config);
    }
}
