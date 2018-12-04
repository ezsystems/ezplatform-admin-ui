<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\EventListener;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\Core\MVC\Symfony\View\Event\FilterViewParametersEvent;
use eZ\Publish\Core\MVC\Symfony\View\ViewEvents;
use EzSystems\EzPlatformAdminUi\View\ContentTranslateView;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * {@inheritdoc}
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

        $contentInfo = $view->getContent()->contentInfo;
        $contentType = $view->getContent()->getContentType();

        $event->getParameterBag()->add([
            'form' => $view->getFormView(),
            'location' => $view->getLocation(),
            'language' => $view->getLanguage(),
            'baseLanguage' => $view->getBaseLanguage(), /** @deprecated In 2.2, will be removed in 3.0. Use `base_language` instead. */
            'base_language' => $view->getBaseLanguage(),
            'content' => $view->getContent(),
            'contentType' => $contentType, /** @deprecated In 2.2, will be removed in 3.0. Use `content_type` instead. */
            'content_type' => $contentType,
            'isPublished' => $contentInfo->isPublished(),  /** @deprecated In 2.2, will be removed in 3.0. Use `ContentInfo::isPublished` in Twig directly. */
        ]);
    }
}
