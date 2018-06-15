<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form;

use EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface;
use EzSystems\EzPlatformAdminUi\UI\Action\EventDispatcherInterface;
use EzSystems\EzPlatformAdminUi\UI\Action\FormUiActionMappingDispatcher;
use EzSystems\EzPlatformAdminUi\UI\Action\UiActionEventInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Exception;

class SubmitHandler
{
    /** @var \EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface */
    protected $notificationHandler;

    /** @var \Symfony\Component\Routing\RouterInterface */
    protected $router;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Action\EventDispatcherInterface */
    protected $uiActionEventDispatcher;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Action\FormUiActionMappingDispatcher */
    protected $formUiActionMappingDispatcher;

    /**
     * @param \EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface $notificationHandler
     * @param \Symfony\Component\Routing\RouterInterface $router
     * @param \EzSystems\EzPlatformAdminUi\UI\Action\EventDispatcherInterface $uiActionEventDispatcher
     * @param \EzSystems\EzPlatformAdminUi\UI\Action\FormUiActionMappingDispatcher $formUiActionMappingDispatcher
     */
    public function __construct(
        NotificationHandlerInterface $notificationHandler,
        RouterInterface $router,
        EventDispatcherInterface $uiActionEventDispatcher,
        FormUiActionMappingDispatcher $formUiActionMappingDispatcher
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->router = $router;
        $this->uiActionEventDispatcher = $uiActionEventDispatcher;
        $this->formUiActionMappingDispatcher = $formUiActionMappingDispatcher;
    }

    /**
     * Wraps business logic with reusable boilerplate code.
     *
     * Handles form errors (NotificationHandler:warning).
     * Handles business logic exceptions (NotificationHandler:error).
     *
     * @param FormInterface $form
     * @param callable(mixed):array $handler
     *
     * @return null|Response
     */
    public function handle(FormInterface $form, callable $handler): ?Response
    {
        $data = $form->getData();

        if ($form->isValid()) {
            try {
                $result = $handler($data);

                if ($result instanceof Response) {
                    $event = $this->formUiActionMappingDispatcher->dispatch($form);
                    $event->setResponse($result);
                    $event->setType(UiActionEventInterface::TYPE_SUCCESS);

                    $this->uiActionEventDispatcher->dispatch($event);

                    return $event->getResponse();
                }
            } catch (Exception $e) {
                $this->notificationHandler->error($e->getMessage());
            }
        } else {
            foreach ($form->getErrors(true, true) as $formError) {
                $this->notificationHandler->warning($formError->getMessage());
            }
        }

        return null;
    }
}
