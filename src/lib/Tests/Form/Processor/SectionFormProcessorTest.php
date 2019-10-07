<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Tests\Form\Processor;

use eZ\Publish\API\Repository\SectionService;
use eZ\Publish\API\Repository\Values\Content\Section;
use EzSystems\EzPlatformAdminUi\Event\FormActionEvent;
use EzSystems\EzPlatformAdminUi\Event\RepositoryFormEvents;
use EzSystems\EzPlatformAdminUi\Form\Processor\SectionFormProcessor;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Section\SectionCreateData;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Section\SectionUpdateData;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormInterface;

class SectionFormProcessorTest extends TestCase
{
    /**
     * @var \eZ\Publish\API\Repository\SectionService|\PHPUnit\Framework\MockObject\MockObject
     */
    private $sectionService;

    /**
     * @var SectionFormProcessor
     */
    private $processor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sectionService = $this->createMock(SectionService::class);
        $this->processor = new SectionFormProcessor($this->sectionService);
    }

    public function testGetSubscribedEvents()
    {
        self::assertSame(
            [
                RepositoryFormEvents::SECTION_UPDATE => ['processUpdate', 10],
            ],
            SectionFormProcessor::getSubscribedEvents()
        );
    }

    public function testProcessCreate()
    {
        $data = new SectionCreateData();
        $newSection = new Section();
        $event = new FormActionEvent($this->createMock(FormInterface::class), $data, 'foo');

        $this->sectionService
            ->expects($this->once())
            ->method('createSection')
            ->with($data)
            ->willReturn($newSection);

        $this->processor->processUpdate($event);
        self::assertSame($newSection, $data->section);
    }

    public function testProcessUpdate()
    {
        $existingSection = new Section();
        $updatedSection = new Section();
        $data = new SectionUpdateData(['section' => $existingSection]);
        $event = new FormActionEvent($this->createMock(FormInterface::class), $data, 'foo');

        $this->sectionService
            ->expects($this->once())
            ->method('updateSection')
            ->with($existingSection, $data)
            ->willReturn($updatedSection);

        $this->processor->processUpdate($event);
        self::assertSame($updatedSection, $data->section);
    }
}
