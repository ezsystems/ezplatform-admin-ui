<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Tests\Form\Processor;

use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\Values\Content\Language;
use EzSystems\EzPlatformAdminUi\Event\FormActionEvent;
use EzSystems\EzPlatformAdminUi\Event\RepositoryFormEvents;
use EzSystems\EzPlatformAdminUi\Form\Processor\LanguageFormProcessor;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Language\LanguageCreateData;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Language\LanguageUpdateData;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormInterface;

class LanguageFormProcessorTest extends TestCase
{
    /**
     * @var \eZ\Publish\API\Repository\LanguageService|\PHPUnit\Framework\MockObject\MockObject
     */
    private $languageService;

    /**
     * @var LanguageFormProcessor
     */
    private $processor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->languageService = $this->createMock(LanguageService::class);
        $this->processor = new LanguageFormProcessor($this->languageService);
    }

    public function testGetSubscribedEvents()
    {
        self::assertSame(
            [
                RepositoryFormEvents::LANGUAGE_UPDATE => ['processUpdate', 10],
            ],
            LanguageFormProcessor::getSubscribedEvents()
        );
    }

    public function testProcessCreate()
    {
        $data = new LanguageCreateData();
        $newLanguage = new Language();
        $event = new FormActionEvent($this->createMock(FormInterface::class), $data, 'foo');

        $this->languageService
            ->expects($this->once())
            ->method('createLanguage')
            ->with($data)
            ->willReturn($newLanguage);

        $this->processor->processUpdate($event);
        self::assertSame($newLanguage, $data->language);
    }

    public function testProcessUpdate()
    {
        $existingLanguage = new Language();
        $updatedLanguage = new Language();
        $data = new LanguageUpdateData(['language' => $existingLanguage, 'name' => 'update']);
        $event = new FormActionEvent($this->createMock(FormInterface::class), $data, 'foo');

        $this->languageService
            ->expects($this->once())
            ->method('updateLanguageName')
            ->with($existingLanguage, 'update')
            ->willReturn($updatedLanguage);

        $this->languageService
            ->expects($this->once())
            ->method('enableLanguage')
            ->with($existingLanguage)
            ->willReturn($updatedLanguage);

        $this->processor->processUpdate($event);
        self::assertSame($updatedLanguage, $data->language);
    }
}
