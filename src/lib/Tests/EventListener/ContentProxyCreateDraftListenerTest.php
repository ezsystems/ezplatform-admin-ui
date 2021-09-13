<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tests\EventListener;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinitionCollection;
use eZ\Publish\SPI\FieldType\Value;
use EzSystems\EzPlatformAdminUi\Event\ContentProxyCreateEvent;
use EzSystems\EzPlatformAdminUi\Event\ContentProxyTranslateEvent;
use EzSystems\EzPlatformAdminUi\Event\Options;
use EzSystems\EzPlatformAdminUi\EventListener\ContentProxyCreateDraftListener;
use EzSystems\EzPlatformAdminUi\UserSetting\Autosave;
use EzSystems\EzPlatformUser\UserSetting\UserSetting;
use EzSystems\EzPlatformUser\UserSetting\UserSettingService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

final class ContentProxyCreateDraftListenerTest extends TestCase
{
    public function testCreateContentAutosaveEnabled(): void
    {
        $userSettingService = $this->createMock(UserSettingService::class);
        $userSettingService
            ->method('getUserSetting')
            ->with('autosave')
            ->willReturn(
                new UserSetting([
                    'value' => Autosave::ENABLED_OPTION,
                ])
            );

        $contentService = $this->createMock(ContentService::class);
        $contentService
            ->method('createContent')
            ->willReturn($this->createMock(Content::class));

        $router = $this->createMock(RouterInterface::class);
        $router
            ->method('generate')
            ->with('ezplatform.content.draft.edit', [
                'contentId' => null,
                'versionNo' => null,
                'language' => 'eng-EN',
            ])
            ->willReturn('redirect_test_url');

        $createEvent = new ContentProxyCreateEvent(
            $this->createMock(ContentType::class),
            'eng-EN',
            1234
        );

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber(
            new ContentProxyCreateDraftListener(
                $contentService,
                $this->createMock(LocationService::class),
                $userSettingService,
                $router
            )
        );

        $eventDispatcher->dispatch($createEvent);

        self::assertEquals(new RedirectResponse('redirect_test_url'), $createEvent->getResponse());
    }

    public function testCreateContentOnTheFlyAutosaveEnabled(): void
    {
        $userSettingService = $this->createMock(UserSettingService::class);
        $userSettingService
            ->method('getUserSetting')
            ->with('autosave')
            ->willReturn(
                new UserSetting([
                    'value' => Autosave::ENABLED_OPTION,
                ])
            );

        $contentInfo = $this->createMock(ContentInfo::class);

        $content = $this->createMock(Content::class);
        $content
            ->method('__get')
            ->will($this->returnCallback(static function ($argument) use ($contentInfo) {
                if ($argument === 'contentInfo') {
                    return $contentInfo;
                }

                return null;
            }));

        $contentService = $this->createMock(ContentService::class);
        $contentService
            ->method('createContent')
            ->willReturn($content);

        $router = $this->createMock(RouterInterface::class);
        $router
            ->method('generate')
            ->with('ezplatform.content_on_the_fly.edit', [
                'contentId' => null,
                'versionNo' => null,
                'languageCode' => 'eng-EN',
                'locationId' => null,
            ])
            ->willReturn('redirect_on_the_fly_test_url');

        $createEvent = new ContentProxyCreateEvent(
            $this->createMock(ContentType::class),
            'eng-EN',
            1234,
            new Options([
                'isOnTheFly' => true,
            ])
        );

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber(
            new ContentProxyCreateDraftListener(
                $contentService,
                $this->createMock(LocationService::class),
                $userSettingService,
                $router
            )
        );

        $eventDispatcher->dispatch($createEvent);

        $this->assertEquals(new RedirectResponse('redirect_on_the_fly_test_url'), $createEvent->getResponse());
    }

    public function testTranslateContentAutosaveEnabled(): void
    {
        $userSettingService = $this->createMock(UserSettingService::class);
        $userSettingService
            ->method('getUserSetting')
            ->with('autosave')
            ->willReturn(
                new UserSetting([
                    'value' => Autosave::ENABLED_OPTION,
                ])
            );

        $contentType = $this->getContentType([
            $this->getFieldDefinition('field_a', true),
        ]);

        $contentInfo = $this->createMock(ContentInfo::class);

        $content = $this->createMock(Content::class);
        $content
            ->method('getContentType')
            ->willReturn($contentType);

        $content
            ->method('getFields')
            ->willReturn([
                new Field([
                    'fieldDefIdentifier' => 'field_a',
                    'value' => 'test',
                ]),
            ]);

        $content
            ->method('__get')
            ->with('contentInfo')
            ->willReturn($contentInfo);

        $contentService = $this->createMock(ContentService::class);
        $contentService
            ->method('loadContent')
            ->with(123, ['eng-GB'])
            ->willReturn($content);

        $contentService
            ->method('createContentDraft')
            ->with($contentInfo)
            ->willReturn($this->createMock(Content::class));

        $router = $this->createMock(RouterInterface::class);
        $router
            ->method('generate')
            ->with('ezplatform.content.draft.edit', [
                'contentId' => null,
                'versionNo' => null,
                'language' => 'pol-PL',
                'locationId' => null,
            ])
            ->willReturn('redirect_test_url');

        $translateEvent = new ContentProxyTranslateEvent(
            123,
            'eng-GB',
            'pol-PL'
        );

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber(
            new ContentProxyCreateDraftListener(
                $contentService,
                $this->createMock(LocationService::class),
                $userSettingService,
                $router
            )
        );

        $eventDispatcher->dispatch($translateEvent, ContentProxyTranslateEvent::class);

        $this->assertEquals(new RedirectResponse('redirect_test_url'), $translateEvent->getResponse());
    }

    public function testAutosaveDisabled(): void
    {
        $userSettingService = $this->createMock(UserSettingService::class);
        $userSettingService
            ->method('getUserSetting')
            ->with('autosave')
            ->willReturn(
                new UserSetting([
                    'value' => Autosave::DISABLED_OPTION,
                ])
            );

        $contentService = $this->createMock(ContentService::class);
        $contentService
            ->expects($this->never())
            ->method('createContent');

        $createEvent = $this->createMock(ContentProxyCreateEvent::class);
        $createEvent
            ->expects($this->never())
            ->method('setResponse');

        $createOnTheFlyEvent = $this->createMock(ContentProxyCreateEvent::class);
        $createOnTheFlyEvent
            ->expects($this->never())
            ->method('setResponse');

        $createOnTheFlyEvent
            ->method('getOptions')
            ->willReturn(new Options([
                'onTheFly' => true,
            ]));

        $translateEvent = $this->createMock(ContentProxyTranslateEvent::class);
        $translateEvent
            ->expects($this->never())
            ->method('setResponse');

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber(
            new ContentProxyCreateDraftListener(
                $contentService,
                $this->createMock(LocationService::class),
                $userSettingService,
                $this->createMock(RouterInterface::class)
            )
        );

        $eventDispatcher->dispatch($createEvent, ContentProxyCreateEvent::class);
        $eventDispatcher->dispatch($createOnTheFlyEvent, ContentProxyCreateEvent::class);
        $eventDispatcher->dispatch($translateEvent, ContentProxyTranslateEvent::class);
    }

    private function getContentType(array $fieldDefs = []): ContentType
    {
        return new ContentType([
            'fieldDefinitions' => new FieldDefinitionCollection($fieldDefs),
        ]);
    }

    private function getFieldDefinition(string $identifier = 'identifier', bool $isTranslatable = false): FieldDefinition
    {
        return new FieldDefinition([
            'identifier' => $identifier,
            'defaultValue' => $this->createMock(Value::class),
            'isTranslatable' => $isTranslatable,
        ]);
    }
}
