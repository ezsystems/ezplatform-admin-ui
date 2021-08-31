<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Tests\ParamConverter;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use EzSystems\EzPlatformAdminUiBundle\ParamConverter\VersionInfoParamConverter;
use Symfony\Component\HttpFoundation\Request;

class VersionInfoParamConverterTest extends AbstractParamConverterTest
{
    const SUPPORTED_CLASS = VersionInfo::class;
    const PARAMETER_NAME = 'versionInfo';

    /** @var \EzSystems\EzPlatformAdminUiBundle\ParamConverter\VersionInfoParamConverter */
    protected $converter;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    protected $serviceMock;

    protected function setUp(): void
    {
        $this->serviceMock = $this->createMock(ContentService::class);

        $this->converter = new VersionInfoParamConverter($this->serviceMock);
    }

    /**
     * @dataProvider dataProvider
     *
     * @param mixed $versionNo
     * @param int $versionNoToload
     * @param mixed $contentId
     * @param int $contentIdToLoad
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function testApply($versionNo, int $versionNoToload, $contentId, int $contentIdToLoad)
    {
        $valueObject = $this->createMock(ContentInfo::class);
        $versionInfo = $this->createMock(VersionInfo::class);

        $this->serviceMock
            ->expects($this->once())
            ->method('loadContentInfo')
            ->with($contentIdToLoad)
            ->willReturn($valueObject);

        $this->serviceMock
            ->expects($this->once())
            ->method('loadVersionInfo')
            ->with($valueObject, $versionNoToload)
            ->willReturn($versionInfo);

        $requestAttributes = [
            VersionInfoParamConverter::PARAMETER_CONTENT_ID => $contentId,
            VersionInfoParamConverter::PARAMETER_VERSION_NO => $versionNo,
        ];

        $request = new Request([], [], $requestAttributes);
        $config = $this->createConfiguration(self::SUPPORTED_CLASS, self::PARAMETER_NAME);

        $this->assertTrue($this->converter->apply($request, $config));
        $this->assertInstanceOf(self::SUPPORTED_CLASS, $request->attributes->get(self::PARAMETER_NAME));
    }

    /**
     * @dataProvider attributeProvider
     *
     * @param $contentId
     * @param $versionNo
     */
    public function testApplyWithWrongAttribute($contentId, $versionNo)
    {
        $requestAttributes = [
            VersionInfoParamConverter::PARAMETER_CONTENT_ID => $contentId,
            VersionInfoParamConverter::PARAMETER_VERSION_NO => $versionNo,
        ];

        $request = new Request([], [], $requestAttributes);
        $config = $this->createConfiguration(self::SUPPORTED_CLASS, self::PARAMETER_NAME);

        $this->assertFalse($this->converter->apply($request, $config));
        $this->assertNull($request->attributes->get(self::PARAMETER_NAME));
    }

    /**
     * @return array
     */
    public function attributeProvider(): array
    {
        return [
            'empty_content_id' => [null, 53],
            'empty_version_no' => [42, null],
        ];
    }

    public function dataProvider(): array
    {
        return [
            'integer' => [53, 53, 42, 42],
            'number_as_string' => ['53', 53, '42', 42],
            'string' => ['53k', 53, '42k', 42],
        ];
    }
}
