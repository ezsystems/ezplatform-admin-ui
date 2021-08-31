<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Tests\ParamConverter;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\SectionService;
use eZ\Publish\API\Repository\Values\Content\Section;
use EzSystems\EzPlatformAdminUiBundle\ParamConverter\SectionParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SectionParamConverterTest extends AbstractParamConverterTest
{
    const SUPPORTED_CLASS = Section::class;
    const PARAMETER_NAME = 'section';

    /** @var \EzSystems\EzPlatformAdminUiBundle\ParamConverter\SectionParamConverter */
    protected $converter;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    protected $serviceMock;

    protected function setUp(): void
    {
        $this->serviceMock = $this->createMock(SectionService::class);

        $this->converter = new SectionParamConverter($this->serviceMock);
    }

    /**
     * @dataProvider dataProvider
     *
     * @param mixed $sectionId The section identifier fetched from the request
     * @param int $sectionIdToLoad The section identifier used to load the section
     */
    public function testApply($sectionId, int $sectionIdToLoad)
    {
        $valueObject = $this->createMock(Section::class);

        $this->serviceMock
            ->expects($this->once())
            ->method('loadSection')
            ->with($sectionIdToLoad)
            ->willReturn($valueObject);

        $requestAttributes = [
            SectionParamConverter::PARAMETER_SECTION_ID => $sectionId,
        ];

        $request = new Request([], [], $requestAttributes);
        $config = $this->createConfiguration(self::SUPPORTED_CLASS, self::PARAMETER_NAME);

        $this->assertTrue($this->converter->apply($request, $config));
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
        $this->expectExceptionMessage(sprintf('Section %s not found.', $sectionId));

        $this->serviceMock
            ->expects($this->once())
            ->method('loadSection')
            ->with($sectionId)
            ->willThrowException($this->createMock(NotFoundException::class));

        $requestAttributes = [
            SectionParamConverter::PARAMETER_SECTION_ID => $sectionId,
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
