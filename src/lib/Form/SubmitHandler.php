<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form;

use Exception;
use eZ\Publish\API\Repository\Exceptions\ForbiddenException;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface;
use EzSystems\EzPlatformAdminUi\UI\Action\EventDispatcherInterface;
use EzSystems\EzPlatformAdminUi\UI\Action\FormUiActionMappingDispatcher;
use EzSystems\EzPlatformAdminUi\UI\Action\UiActionEventInterface;
use EzSystems\EzPlatformUser\Form\SubmitHandler as UserActionsSubmitHandler;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class SubmitHandler implements UserActionsSubmitHandler
{
    /** @var \EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface */
    protected $notificationHandler;

    /** @var \Symfony\Component\Routing\RouterInterface */
    protected $router;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Action\EventDispatcherInterface */
    protected $uiActionEventDispatcher;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Action\FormUiActionMappingDispatcher */
    protected $formUiActionMappingDispatcher;

    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    public function __construct(
        NotificationHandlerInterface $notificationHandler,
        RouterInterface $router,
        EventDispatcherInterface $uiActionEventDispatcher,
        FormUiActionMappingDispatcher $formUiActionMappingDispatcher,
        LoggerInterface $logger
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->router = $router;
        $this->uiActionEventDispatcher = $uiActionEventDispatcher;
        $this->formUiActionMappingDispatcher = $formUiActionMappingDispatcher;
        $this->logger = $logger;
    }

    /**
     * Wraps business logic with reusable boilerplate code.
     *
     * Handles form errors (NotificationHandler:warning).
     * Handles business logic exceptions (NotificationHandler:error).
     *
     * @param \Symfony\Component\Form\FormInterface $form
     * @param callable(mixed):?Response $handler
     *
     * @return \Symfony\Component\HttpFoundation\Response|null
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
            } catch (ForbiddenException | NotFoundException | UnauthorizedException $e) {
                $this->notificationHandler->error(/** @Ignore */ $e->getMessage());
            } catch (Exception $e) {
                $this->logException($e);

                $this->notificationHandler->error(/** @Ignore */ $e->getMessage());
            }
        } else {
            foreach ($form->getErrors(true, true) as $formError) {
                $this->notificationHandler->warning(/** @Ignore */ $formError->getMessage());
            }
        }

        return null;
    }

    /**
     * Wraps business logic with reusable boilerplate code.
     *
     * Handles form errors (JsonResponse(['errors'=> [...], Response::ERROR_STATUS_CODE])).
     * Handles business logic exceptions (JsonResponse(['errors'=> [...], Response::ERROR_STATUS_CODE])).
     *
     * @param \Symfony\Component\Form\FormInterface $form
     * @param callable(mixed):?Response $handler
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function handleAjax(FormInterface $form, callable $handler): JsonResponse
    {
        $data = $form->getData();

        if ($form->isValid()) {
            try {
                /** @var \Symfony\Component\HttpFoundation\JsonResponse $result */
                $result = $handler($data);
                if ($result instanceof JsonResponse && $result->getStatusCode() === Response::HTTP_OK) {
                    $event = $this->formUiActionMappingDispatcher->dispatch($form);
                    $event->setResponse($result);
                    $event->setType(UiActionEventInterface::TYPE_SUCCESS);

                    $this->uiActionEventDispatcher->dispatch($event);

                    return $event->getResponse();
                }

                return $result;
            } catch (Exception $e) {
                $this->logException($e);

                return new JsonResponse([], Response::HTTP_BAD_REQUEST);
            }
        } else {
            $errors = [];
            foreach ($form->getErrors(true, true) as $formError) {
                $errors[] = $formError->getMessage();
            }

            return new JsonResponse(['errors' => $errors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    protected function logException(Exception $e): void
    {
        $this->logger->error(
            'An error has occurred while handling form submission: ' . $e->getMessage(),
            [
                'exception' => $e->getPrevious() ?? $e,
            ]
        );
    }
}
