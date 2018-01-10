<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Tests\ParamConverter;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\ContentType\ContentTypeDraft;
use EzSystems\EzPlatformAdminUiBundle\ParamConverter\ContentTypeDraftParamConverter;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

class ContentTypeDraftParamConverterTest extends AbstractParamConverterTest
{
    const SUPPORTED_CLASS = ContentTypeDraft::class;
    const PARAMETER_NAME = 'contentType';

    /** @var ContentTypeDraftParamConverter */
    protected $converter;

    /** @var MockObject */
    protected $contentTypeServiceMock;

    public function setUp()
    {
        $this->contentTypeServiceMock = $this->createMock(ContentTypeService::class);

        $this->converter = new ContentTypeDraftParamConverter($this->contentTypeServiceMock);
    }

    public function testApply()
    {
        $contentTypeId = 42;
        $valueObject = $this->createMock(ContentTypeDraft::class);

        $this->contentTypeServiceMock
            ->expects($this->once())
            ->method('loadContentTypeDraft')
            ->with($contentTypeId)
            ->willReturn($valueObject);

        $requestAttributes = [
            ContentTypeDraftParamConverter::PARAMETER_CONTENT_TYPE_ID => $contentTypeId,
        ];

        $request = new Request([], [], $requestAttributes);
        $config = $this->createConfiguration(self::SUPPORTED_CLASS, self::PARAMETER_NAME);

        $this->converter->apply($request, $config);

        $this->assertInstanceOf(self::SUPPORTED_CLASS, $request->attributes->get(self::PARAMETER_NAME));
    }

    public function testApplyWithWrongAttribute()
    {
        $requestAttributes = [
            ContentTypeDraftParamConverter::PARAMETER_CONTENT_TYPE_ID => null,
        ];

        $request = new Request([], [], $requestAttributes);
        $config = $this->createConfiguration(self::SUPPORTED_CLASS, self::PARAMETER_NAME);

        $this->assertFalse($this->converter->apply($request, $config));
        $this->assertNull($request->attributes->get(self::PARAMETER_NAME));
    }
}
