<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\EventListener;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Field;
use EzSystems\EzPlatformAdminUi\Event\ContentProxyCreateEvent;
use EzSystems\EzPlatformAdminUi\Event\ContentProxyTranslateEvent;
use EzSystems\EzPlatformAdminUi\UserSetting\Autosave as AutosaveSetting;
use EzSystems\EzPlatformUser\UserSetting\UserSettingService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class ContentProxyCreateDraftListener implements EventSubscriberInterface
{
    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    /** @var \EzSystems\EzPlatformUser\UserSetting\UserSettingService */
    private $userSettingService;

    /** @var \Symfony\Component\Routing\RouterInterface */
    private $router;

    public function __construct(
        ContentService $contentService,
        LocationService $locationService,
        UserSettingService $userSettingService,
        RouterInterface $router
    ) {
        $this->contentService = $contentService;
        $this->locationService = $locationService;
        $this->userSettingService = $userSettingService;
        $this->router = $router;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ContentProxyCreateEvent::class => 'create',
            ContentProxyTranslateEvent::class => 'translate',
        ];
    }

    public function create(ContentProxyCreateEvent $event): void
    {
        $isAutosaveEnabled = $this->userSettingService->getUserSetting('autosave')->value === AutosaveSetting::ENABLED_OPTION;

        if (!$isAutosaveEnabled) {
            return;
        }

        $options = $event->getOptions();

        $createContentStruct = $this->contentService->newContentCreateStruct(
            $event->getContentType(),
            $event->getLanguageCode()
        );

        $contentDraft = $this->contentService->createContent(
            $createContentStruct,
            [
                $this->locationService->newLocationCreateStruct($event->getParentLocationId()),
            ],
            []
        );

        if ($options->get('isOnTheFly', false)) {
            $response = new RedirectResponse(
                $this->router->generate('ezplatform.content_on_the_fly.edit', [
                    'contentId' => $contentDraft->id,
                    'versionNo' => $contentDraft->getVersionInfo()->versionNo,
                    'languageCode' => $event->getLanguageCode(),
                    'locationId' => $contentDraft->contentInfo->mainLocationId,
                ])
            );
        } else {
            $response = new RedirectResponse(
                $this->router->generate('ezplatform.content.draft.edit', [
                    'contentId' => $contentDraft->id,
                    'versionNo' => $contentDraft->getVersionInfo()->versionNo,
                    'language' => $event->getLanguageCode(),
                ])
            );
        }

        $event->setResponse($response);
    }

    public function translate(ContentProxyTranslateEvent $event): void
    {
        $isAutosaveEnabled = $this->userSettingService->getUserSetting('autosave')->value === AutosaveSetting::ENABLED_OPTION;

        if (!$isAutosaveEnabled) {
            return;
        }

        $content = $this->contentService->loadContent(
            $event->getContentId(),
            $event->getFromLanguageCode() !== null
                ? [$event->getFromLanguageCode()]
                : null
        );

        $toLanguageCode = $event->getToLanguageCode();

        $contentUpdateStruct = $this->contentService->newContentUpdateStruct();
        $contentUpdateStruct->initialLanguageCode = $toLanguageCode;
        $contentUpdateStruct->fields = $this->getTranslatedContentFields($content, $toLanguageCode);

        $contentDraft = $this->contentService->createContentDraft($content->contentInfo);

        $this->contentService->updateContent(
            $contentDraft->getVersionInfo(),
            $contentUpdateStruct,
            []
        );

        $response = new RedirectResponse(
            $this->router->generate('ezplatform.content.draft.edit', [
                'contentId' => $contentDraft->id,
                'versionNo' => $contentDraft->getVersionInfo()->versionNo,
                'language' => $toLanguageCode,
                'locationId' => $event->getLocationId(),
            ])
        );

        $event->setResponse($response);
    }

    private function getTranslatedContentFields(Content $content, string $languageCode): array
    {
        $contentType = $content->getContentType();

        $translatableFields = array_filter($content->getFields(), static function (Field $field) use ($contentType): bool {
            return $contentType->getFieldDefinition($field->fieldDefIdentifier)->isTranslatable;
        });

        return array_map(static function (Field $field) use ($languageCode): Field {
            return new Field([
                'value' => $field->value,
                'fieldDefIdentifier' => $field->fieldDefIdentifier,
                'fieldTypeIdentifier' => $field->fieldTypeIdentifier,
                'languageCode' => $languageCode,
            ]);
        }, $translatableFields);
    }
}
