<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use EzSystems\EzPlatformAdminUi\Event\ContentProxyTranslateEvent;
use EzSystems\EzPlatformAdminUi\View\ContentTranslateSuccessView;
use EzSystems\EzPlatformAdminUi\View\ContentTranslateView;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ContentEditController extends Controller
{
    /** @var \Symfony\Contracts\EventDispatcher\EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function proxyTranslateAction(
        int $contentId,
        ?string $fromLanguageCode,
        string $toLanguageCode
    ): Response {
        /** @var \EzSystems\EzPlatformAdminUi\Event\ContentProxyTranslateEvent $event */
        $event = $this->eventDispatcher->dispatch(
            new ContentProxyTranslateEvent(
                $contentId,
                $fromLanguageCode,
                $toLanguageCode
            )
        );

        if ($event->hasResponse()) {
            return $event->getResponse();
        }

        // Fallback to "translate"
        return $this->redirectToRoute('ezplatform.content.translate', [
            'contentId' => $contentId,
            'fromLanguageCode' => $fromLanguageCode,
            'toLanguageCode' => $toLanguageCode,
        ]);
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\View\ContentTranslateView $view
     *
     * @return \EzSystems\EzPlatformAdminUi\View\ContentTranslateView
     */
    public function translateAction(ContentTranslateView $view): ContentTranslateView
    {
        return $view;
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\View\ContentTranslateSuccessView $view
     *
     * @return \EzSystems\EzPlatformAdminUi\View\ContentTranslateSuccessView
     */
    public function translationSuccessAction(ContentTranslateSuccessView $view): ContentTranslateSuccessView
    {
        return $view;
    }
}
