<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface;
use EzSystems\EzPlatformAdminUi\UI\Action\EventDispatcherInterface;
use EzSystems\EzPlatformAdminUi\UI\Action\FormUiActionMappingDispatcher;
use EzSystems\EzPlatformAdminUi\UI\Action\UiActionEventInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Exception;

class SubmitHandler implements LoggerAwareInterface
{
    use LoggerAwareTrait;

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
        $this->logger = new NullLogger();
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
     * @return Response|null
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
            } catch (NotFoundException | UnauthorizedException $e) {
                $this->notificationHandler->error(/** @Ignore */ $e->getMessage());
            } catch (Exception $e) {
                $this->logger->error('An error has occurred while handling form submission: ' . $e->getMessage(), [
                    'exception' => $e,
                ]);

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
     * @param FormInterface $form
     * @param callable(mixed):array $handler
     *
     * @return JsonResponse
     */
    public function handleAjax(FormInterface $form, callable $handler): JsonResponse
    {
        $data = $form->getData();

        if ($form->isValid()) {
            try {
                /** @var JsonResponse $result */
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
                $this->logger->error('An error has occurred while handling form submission: ' . $e->getMessage(), [
                    'exception' => $e,
                ]);

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
}
