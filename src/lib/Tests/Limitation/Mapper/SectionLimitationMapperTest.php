<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Tests\Limitation\Mapper;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\SectionService;
use eZ\Publish\API\Repository\Values\Content\Section;
use eZ\Publish\API\Repository\Values\User\Limitation\SectionLimitation;
use EzSystems\EzPlatformAdminUi\Limitation\Mapper\SectionLimitationMapper;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class SectionLimitationMapperTest extends TestCase
{
    private const EXAMPLE_SECTION_ID = 0xFF;

    /** @var \PHPUnit\Framework\MockObject\MockObject|\eZ\Publish\API\Repository\SectionService */
    private $sectionServiceMock;

    /** @var \Psr\Log\LoggerInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $logger;

    /** @var \EzSystems\EzPlatformAdminUi\Limitation\Mapper\SectionLimitationMapper */
    private $mapper;

    protected function setUp(): void
    {
        $this->sectionServiceMock = $this->createMock(SectionService::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->mapper = new SectionLimitationMapper($this->sectionServiceMock);
        $this->mapper->setLogger($this->logger);
    }

    public function testMapLimitationValue()
    {
        $values = ['3', '5', '7'];

        $expected = [];
        foreach ($values as $i => $value) {
            $expected[$i] = new Section([
                'id' => $value,
            ]);

            $this->sectionServiceMock
                ->expects($this->at($i))
                ->method('loadSection')
                ->with($value)
                ->willReturn($expected[$i]);
        }

        $result = $this->mapper->mapLimitationValue(new SectionLimitation([
            'limitationValues' => $values,
        ]));

        $this->assertEquals($expected, $result);
    }

    public function testMapLimitationValueWithNotExistingContentType()
    {
        $values = [self::EXAMPLE_SECTION_ID];

        $this->sectionServiceMock
            ->expects($this->once())
            ->method('loadSection')
            ->with($values[0])
            ->willThrowException($this->createMock(NotFoundException::class));

        $this->logger
            ->expects($this->once())
            ->method('error')
            ->with('Could not map the Limitation value: could not find a Section with ID ' . $values[0]);

        $actual = $this->mapper->mapLimitationValue(new SectionLimitation([
            'limitationValues' => $values,
        ]));

        $this->assertEmpty($actual);
    }
}
