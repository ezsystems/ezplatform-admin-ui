<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Tests\ParamConverter;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup;
use EzSystems\EzPlatformAdminUiBundle\ParamConverter\ContentTypeGroupParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ContentTypeGroupParamConverterTest extends AbstractParamConverterTest
{
    const SUPPORTED_CLASS = ContentTypeGroup::class;
    const PARAMETER_NAME = 'contentTypeGroup';

    /** @var \EzSystems\EzPlatformAdminUiBundle\ParamConverter\ContentTypeGroupParamConverter */
    protected $converter;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    protected $serviceMock;

    protected function setUp(): void
    {
        $this->serviceMock = $this->createMock(ContentTypeService::class);

        $this->converter = new ContentTypeGroupParamConverter($this->serviceMock);
    }

    /**
     * @dataProvider dataProvider
     *
     * @param mixed $contentTypeGroupId The identifier fetched from the request
     * @param int $contentTypeGroupIdToLoad The identifier used to load the Content Type Group
     */
    public function testApply($contentTypeGroupId, int $contentTypeGroupIdToLoad)
    {
        $valueObject = $this->createMock(ContentTypeGroup::class);

        $this->serviceMock
            ->expects($this->once())
            ->method('loadContentTypeGroup')
            ->with($contentTypeGroupIdToLoad)
            ->willReturn($valueObject);

        $requestAttributes = [
            ContentTypeGroupParamConverter::PARAMETER_CONTENT_TYPE_GROUP_ID => $contentTypeGroupId,
        ];

        $request = new Request([], [], $requestAttributes);
        $config = $this->createConfiguration(self::SUPPORTED_CLASS, self::PARAMETER_NAME);

        $this->assertTrue($this->converter->apply($request, $config));
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
        $this->expectExceptionMessage(
            sprintf('Content Type group %s not found.', $contentTypeGroupId)
        );

        $this->serviceMock
            ->expects($this->once())
            ->method('loadContentTypeGroup')
            ->with($contentTypeGroupId)
            ->willThrowException(
                $this->createMock(NotFoundException::class)
            );

        $requestAttributes = [
            ContentTypeGroupParamConverter::PARAMETER_CONTENT_TYPE_GROUP_ID => $contentTypeGroupId,
        ];

        $request = new Request([], [], $requestAttributes);
        $config = $this->createConfiguration(self::SUPPORTED_CLASS, self::PARAMETER_NAME);

        $this->converter->apply($request, $config);
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
