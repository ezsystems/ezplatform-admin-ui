<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\EventListener;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\Core\MVC\Symfony\View\Event\FilterViewParametersEvent;
use eZ\Publish\Core\MVC\Symfony\View\ViewEvents;
use EzSystems\EzPlatformAdminUi\View\ContentTranslateView;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @inheritdoc
 */
class ContentTranslateViewFilterParametersListener implements EventSubscriberInterface
{
    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    protected $contentTypeService;

    /**
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     */
    public function __construct(
        ContentTypeService $contentTypeService
    ) {
        $this->contentTypeService = $contentTypeService;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ViewEvents::FILTER_VIEW_PARAMETERS => ['onFilterViewParameters', 10],
        ];
    }

    /**
     * @param \eZ\Publish\Core\MVC\Symfony\View\Event\FilterViewParametersEvent $event
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function onFilterViewParameters(FilterViewParametersEvent $event)
    {
        $view = $event->getView();

        if (!$view instanceof ContentTranslateView) {
            return;
        }

        $contentType = $view->getContent()->getContentType();

        $event->getParameterBag()->add([
            'form' => $view->getFormView(),
            'location' => $view->getLocation(),
            'language' => $view->getLanguage(),
            'base_language' => $view->getBaseLanguage(),
            'content' => $view->getContent(),
            'content_type' => $contentType,
        ]);
    }
}
