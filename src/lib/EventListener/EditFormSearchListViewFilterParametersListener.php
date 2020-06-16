<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\EventListener;

use eZ\Publish\Core\MVC\Symfony\View\Event\FilterViewParametersEvent;
use eZ\Publish\Core\MVC\Symfony\View\ViewEvents;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Draft\ContentEditData;
use EzSystems\EzPlatformAdminUi\Form\Type\Content\Draft\ContentEditType;
use Ibexa\Platform\Search\View\SearchListView;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormFactoryInterface;

final class EditFormSearchListViewFilterParametersListener implements EventSubscriberInterface
{
    /** @var \Symfony\Component\Form\FormFactoryInterface */
    private $formFactory;

    public function __construct(
        FormFactoryInterface $formFactory
    ) {
        $this->formFactory = $formFactory;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ViewEvents::FILTER_VIEW_PARAMETERS => ['onFilterViewParameters', 10],
        ];
    }

    public function onFilterViewParameters(FilterViewParametersEvent $event)
    {
        $view = $event->getView();

        if (!$view instanceof SearchListView) {
            return;
        }

        $editForm = $this->formFactory->create(
            ContentEditType::class,
            new ContentEditData(),
        );

        $event->getParameterBag()->add([
            'form_edit' => $editForm->createView(),
        ]);
    }
}
