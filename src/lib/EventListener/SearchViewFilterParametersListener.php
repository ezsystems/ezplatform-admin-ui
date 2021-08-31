<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\EventListener;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\Core\MVC\Symfony\View\Event\FilterViewParametersEvent;
use eZ\Publish\Core\MVC\Symfony\View\ViewEvents;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Draft\ContentEditData;
use EzSystems\EzPlatformAdminUi\Form\Type\Content\Draft\ContentEditType;
use EzSystems\EzPlatformAdminUi\Specification\SiteAccess\IsAdmin;
use Ibexa\Platform\Search\View\SearchView;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class SearchViewFilterParametersListener implements EventSubscriberInterface
{
    /** @var \Symfony\Component\Form\FormFactoryInterface */
    private $formFactory;

    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    /** @var \Symfony\Component\HttpFoundation\RequestStack */
    private $requestStack;

    /** @var string[][] */
    private $siteAccessGroups;

    public function __construct(
        FormFactoryInterface $formFactory,
        ConfigResolverInterface $configResolver,
        RequestStack $requestStack,
        array $siteAccessGroups
    ) {
        $this->formFactory = $formFactory;
        $this->configResolver = $configResolver;
        $this->requestStack = $requestStack;
        $this->siteAccessGroups = $siteAccessGroups;
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

        if (!$view instanceof SearchView) {
            return;
        }

        if (!$this->isAdminSiteAccess($this->requestStack->getCurrentRequest())) {
            return;
        }

        $editForm = $this->formFactory->create(
            ContentEditType::class,
            new ContentEditData(),
        );

        $event->getParameterBag()->add([
            'form_edit' => $editForm->createView(),
            'user_content_type_identifier' => $this->configResolver->getParameter('user_content_type_identifier'),
        ]);
    }

    private function isAdminSiteAccess(Request $request): bool
    {
        return (new IsAdmin($this->siteAccessGroups))->isSatisfiedBy($request->attributes->get('siteaccess'));
    }
}
