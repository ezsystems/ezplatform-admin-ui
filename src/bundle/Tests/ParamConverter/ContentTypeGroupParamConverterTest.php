<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Tests\ParamConverter;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup;
use EzSystems\EzPlatformAdminUiBundle\ParamConverter\ContentTypeGroupParamConverter;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ContentTypeGroupParamConverterTest extends AbstractParamConverterTest
{
    const SUPPORTED_CLASS = ContentTypeGroup::class;
    const PARAMETER_NAME = 'contentTypeGroup';

    /** @var ContentTypeGroupParamConverter */
    protected $converter;

    /** @var MockObject */
    protected $serviceMock;

    public function setUp()
    {
        $this->serviceMock = $this->createMock(ContentTypeService::class);

        $this->converter = new ContentTypeGroupParamConverter($this->serviceMock);
    }

    public function testApply()
    {
        $contentTypeGroupId = 42;
        $valueObject = $this->createMock(ContentTypeGroup::class);

        $this->serviceMock
            ->expects($this->once())
            ->method('loadContentTypeGroup')
            ->with($contentTypeGroupId)
            ->willReturn($valueObject);

        $requestAttributes = [
            ContentTypeGroupParamConverter::PARAMETER_CONTENT_TYPE_GROUP_ID => $contentTypeGroupId,
        ];

        $request = new Request([], [], $requestAttributes);
        $config = $this->createConfiguration(self::SUPPORTED_CLASS, self::PARAMETER_NAME);

        $this->converter->apply($request, $config);

        $this->assertInstanceOf(self::SUPPORTED_CLASS, $request->attributes->get(self::PARAMETER_NAME));
    }

    public function testApplyWithWrongAttribute()
    {
        $requestAttributes = [
            ContentTypeGroupParamConverter::PARAMETER_CONTENT_TYPE_GROUP_ID => null,
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
        $this->expectExceptionMessage(sprintf('ContentTypeGroup %s not found!', $contentTypeGroupId));

        $this->serviceMock
            ->expects($this->once())
            ->method('loadContentTypeGroup')
            ->with($contentTypeGroupId)
            ->willReturn(null);

        $requestAttributes = [
            ContentTypeGroupParamConverter::PARAMETER_CONTENT_TYPE_GROUP_ID => $contentTypeGroupId,
        ];

        $request = new Request([], [], $requestAttributes);
        $config = $this->createConfiguration(self::SUPPORTED_CLASS, self::PARAMETER_NAME);

        $this->converter->apply($request, $config);
    }
}
