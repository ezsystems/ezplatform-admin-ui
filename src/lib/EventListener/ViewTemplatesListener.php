<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\EventListener;

use eZ\Publish\Core\MVC\Symfony\Event\PreContentViewEvent;
use eZ\Publish\Core\MVC\Symfony\MVCEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Sets the templates used by the user controller.
 */
class ViewTemplatesListener implements EventSubscriberInterface
{
    /**
     * Hash of [View type FQN] => template.
     *
     * @var array
     */
    protected $viewTemplates;

    /** @var string */
    protected $pagelayout;

    public static function getSubscribedEvents(): array
    {
        return [MVCEvents::PRE_CONTENT_VIEW => 'setViewTemplates'];
    }

    public function setViewTemplate(string $viewClass, string $template): void
    {
        $this->viewTemplates[$viewClass] = $template;
    }

    public function setPagelayout(string $pagelayout): void
    {
        $this->pagelayout = $pagelayout;
    }

    /**
     * If the event's view has a defined template, sets the view's template identifier,
     * and the 'pagelayout' parameter.
     */
    public function setViewTemplates(PreContentViewEvent $event): void
    {
        $view = $event->getContentView();

        foreach ($this->viewTemplates as $viewClass => $template) {
            if ($view instanceof $viewClass) {
                $view->setTemplateIdentifier($template);
                $view->addParameters(['pagelayout' => $this->pagelayout]);
                $view->addParameters(['page_layout' => $this->pagelayout]);
            }
        }
    }
}
