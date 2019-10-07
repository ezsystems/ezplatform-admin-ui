<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace EzSystems\EzPlatformAdminUi\Form\ActionDispatcher;

use eZ\Publish\API\Repository\Values\ValueObject;
use EzSystems\EzPlatformAdminUi\Event\FormActionEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Base class for action dispatchers.
 */
abstract class AbstractActionDispatcher implements ActionDispatcherInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var \Symfony\Component\HttpFoundation\Response
     */
    protected $response;

    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function dispatchFormAction(FormInterface $form, ValueObject $data, $actionName = null, array $options = [])
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $options = $resolver->resolve($options);

        // First dispatch default action, then $actionName.
        $event = new FormActionEvent($form, $data, $actionName, $options);
        $defaultActionEventName = $this->getActionEventBaseName();
        $this->dispatchDefaultAction($defaultActionEventName, $event);
        // Action name is not set e.g. when pressing return in a text field.
        // We have already run the default action, no need to run it again in that case.
        if ($actionName) {
            $this->dispatchAction("$defaultActionEventName.$actionName", $event);
        }
        $this->response = $event->getResponse();
    }

    /**
     * Configures options to pass to the form action event.
     * Might do nothing if there are no options.
     *
     * @param OptionsResolver $resolver
     */
    abstract protected function configureOptions(OptionsResolver $resolver);

    /**
     * Returns base for action event name. It will be used as default action event name.
     * By convention, other action event names will have the format "<actionEventBaseName>.<actionName>".
     *
     * @return string
     */
    abstract protected function getActionEventBaseName();

    /**
     * @param $defaultActionEventName
     * @param $event
     */
    protected function dispatchDefaultAction($defaultActionEventName, FormActionEvent $event)
    {
        $this->eventDispatcher->dispatch($event, $defaultActionEventName);
    }

    /**
     * @param $actionEventName
     * @param $event
     */
    protected function dispatchAction($actionEventName, FormActionEvent $event)
    {
        $this->eventDispatcher->dispatch($event, $actionEventName);
    }

    public function getResponse()
    {
        return $this->response;
    }
}
