<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Tests\ParamConverter;

use eZ\Publish\API\Repository\SectionService;
use eZ\Publish\API\Repository\Values\Content\Section;
use EzSystems\EzPlatformAdminUiBundle\ParamConverter\SectionParamConverter;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SectionParamConverterTest extends AbstractParamConverterTest
{
    const SUPPORTED_CLASS = Section::class;
    const PARAMETER_NAME = 'section';

    /** @var SectionParamConverter */
    protected $converter;

    /** @var MockObject */
    protected $serviceMock;

    public function setUp()
    {
        $this->serviceMock = $this->createMock(SectionService::class);

        $this->converter = new SectionParamConverter($this->serviceMock);
    }

    public function testApply()
    {
        $sectionId = 42;
        $valueObject = $this->createMock(Section::class);

        $this->serviceMock
            ->expects($this->once())
            ->method('loadSection')
            ->with($sectionId)
            ->willReturn($valueObject);

        $requestAttributes = [
            SectionParamConverter::PARAMETER_SECTION_ID => $sectionId,
        ];

        $request = new Request([], [], $requestAttributes);
        $config = $this->createConfiguration(self::SUPPORTED_CLASS, self::PARAMETER_NAME);

        $this->converter->apply($request, $config);

        $this->assertInstanceOf(self::SUPPORTED_CLASS, $request->attributes->get(self::PARAMETER_NAME));
    }

    public function testApplyWithWrongAttribute()
    {
        $requestAttributes = [
            SectionParamConverter::PARAMETER_SECTION_ID => null,
        ];

        $request = new Request([], [], $requestAttributes);
        $config = $this->createConfiguration(self::SUPPORTED_CLASS, self::PARAMETER_NAME);

        $this->assertFalse($this->converter->apply($request, $config));
        $this->assertNull($request->attributes->get(self::PARAMETER_NAME));
    }

    public function testApplyWhenNotFound()
    {
        $sectionId = 42;

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage(sprintf('Section %s not found!', $sectionId));

        $this->serviceMock
            ->expects($this->once())
            ->method('loadSection')
            ->with($sectionId)
            ->willReturn(null);

        $requestAttributes = [
            SectionParamConverter::PARAMETER_SECTION_ID => $sectionId,
        ];

        $request = new Request([], [], $requestAttributes);
        $config = $this->createConfiguration(self::SUPPORTED_CLASS, self::PARAMETER_NAME);

        $this->converter->apply($request, $config);
    }
}
