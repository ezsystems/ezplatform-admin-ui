<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Tests\ParamConverter;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use EzSystems\EzPlatformAdminUiBundle\ParamConverter\VersionInfoParamConverter;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use eZ\Publish\API\Repository\ContentService;

class VersionInfoParamConverterTest extends AbstractParamConverterTest
{
    const SUPPORTED_CLASS = VersionInfo::class;
    const PARAMETER_NAME = 'versionInfo';

    /** @var VersionInfoParamConverter */
    protected $converter;

    /** @var MockObject */
    protected $serviceMock;

    public function setUp()
    {
        $this->serviceMock = $this->createMock(ContentService::class);

        $this->converter = new VersionInfoParamConverter($this->serviceMock);
    }

    public function testApply()
    {
        $contentId = 42;
        $versionNo = 53;
        $valueObject = $this->createMock(ContentInfo::class);
        $versionInfo = $this->createMock(VersionInfo::class);

        $this->serviceMock
            ->expects($this->once())
            ->method('loadContentInfo')
            ->with($contentId)
            ->willReturn($valueObject);

        $this->serviceMock
            ->expects($this->once())
            ->method('loadVersionInfo')
            ->with($valueObject, $versionNo)
            ->willReturn($versionInfo);

        $requestAttributes = [
            VersionInfoParamConverter::PARAMETER_CONTENT_ID => $contentId,
            VersionInfoParamConverter::PARAMETER_VERSION_NO => $versionNo,
        ];

        $request = new Request([], [], $requestAttributes);
        $config = $this->createConfiguration(self::SUPPORTED_CLASS, self::PARAMETER_NAME);

        $this->converter->apply($request, $config);

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
}
