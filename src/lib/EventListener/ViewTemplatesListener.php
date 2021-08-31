<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\EventListener;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\Core\MVC\Symfony\Event\PreContentViewEvent;
use eZ\Publish\Core\MVC\Symfony\MVCEvents;
use EzSystems\EzPlatformContentForms\User\View\UserCreateView;
use EzSystems\EzPlatformContentForms\User\View\UserUpdateView;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Sets the templates used by the user controller.
 */
class ViewTemplatesListener implements EventSubscriberInterface
{
    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    public function __construct(ConfigResolverInterface $configResolver)
    {
        $this->configResolver = $configResolver;
    }

    public static function getSubscribedEvents(): array
    {
        return [MVCEvents::PRE_CONTENT_VIEW => 'setViewTemplates'];
    }

    /**
     * If the event's view has a defined template, sets the view's template identifier,
     * and the 'pagelayout' parameter.
     */
    public function setViewTemplates(PreContentViewEvent $event): void
    {
        $view = $event->getContentView();
        $pagelayout = $this->configResolver->getParameter('pagelayout');

        foreach ($this->getTemplatesMap() as $viewClass => $template) {
            if ($view instanceof $viewClass) {
                $view->setTemplateIdentifier($template);
                $view->addParameters(['pagelayout' => $pagelayout]);
                $view->addParameters(['page_layout' => $pagelayout]);
            }
        }
    }

    /**
     * @return string[]
     */
    private function getTemplatesMap(): array
    {
        return [
            UserCreateView::class => $this->configResolver->getParameter('user_edit.templates.create'),
            UserUpdateView::class => $this->configResolver->getParameter('user_edit.templates.update'),
        ];
    }
}
