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

    /**
     * @var string
     */
    protected $pagelayout;

    public static function getSubscribedEvents()
    {
        return [MVCEvents::PRE_CONTENT_VIEW => 'setViewTemplates'];
    }

    /**
     * Sets the $template to use for objects of class $viewClass.
     *
     * @param string $viewClass FQN of a View class
     * @param string $template
     */
    public function setViewTemplate($viewClass, $template)
    {
        $this->viewTemplates[$viewClass] = $template;
    }

    /**
     * Sets the pagelayout template to assign to views.
     *
     * @param string $pagelayout
     */
    public function setPagelayout($pagelayout)
    {
        $this->pagelayout = $pagelayout;
    }

    /**
     * If the event's view has a defined template, sets the view's template identifier,
     * and the 'pagelayout' parameter.
     *
     * @param PreContentViewEvent $event
     */
    public function setViewTemplates(PreContentViewEvent $event)
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
