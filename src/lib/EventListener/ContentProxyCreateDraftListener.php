<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\EventListener;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\LocationService;
use EzSystems\EzPlatformAdminUi\Event\ContentProxyCreateEvent;
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
            ContentProxyCreateEvent::class => 'createDraft',
        ];
    }

    public function createDraft(ContentProxyCreateEvent $event)
    {
        $isAutosaveEnabled = $this->userSettingService->getUserSetting('autosave')->value === AutosaveSetting::ENABLED_OPTION;

        if (!$isAutosaveEnabled) {
            return;
        }

        $createContentTypeStuct = $this->contentService->newContentCreateStruct(
            $event->getContentType(),
            $event->getLanguageCode()
        );

        $contentDraft = $this->contentService->createContent(
            $createContentTypeStuct,
            [
                $this->locationService->newLocationCreateStruct($event->getParentLocationId()),
            ],
            []
        );

        if (!$event->getOptions()->get('isOnTheFly', false)) {
            $response = new RedirectResponse(
                $this->router->generate('ezplatform.content.draft.edit', [
                    'contentId' => $contentDraft->id,
                    'versionNo' => $contentDraft->getVersionInfo()->versionNo,
                    'language' => $event->getLanguageCode(),
                ])
            );
        } else {
            $response = new RedirectResponse(
                $this->router->generate('ezplatform.content_on_the_fly.edit', [
                    'contentId' => $contentDraft->id,
                    'versionNo' => $contentDraft->getVersionInfo()->versionNo,
                    'languageCode' => $event->getLanguageCode(),
                    'locationId' => $contentDraft->contentInfo->mainLocationId,
                ])
            );
        }

        $event->setResponse($response);
    }
}
