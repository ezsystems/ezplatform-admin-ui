<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form;

use EzSystems\EzPlatformAdminUi\Form\Data\OnFailureRedirect;
use EzSystems\EzPlatformAdminUi\Form\Data\OnSuccessRedirect;
use EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Exception;

class SubmitHandler
{
    /** @var NotificationHandlerInterface */
    protected $notificationHandler;

    /** @var RouterInterface */
    protected $router;

    /**
     * @param NotificationHandlerInterface $notificationHandler
     * @param RouterInterface $router
     */
    public function __construct(NotificationHandlerInterface $notificationHandler, RouterInterface $router)
    {
        $this->notificationHandler = $notificationHandler;
        $this->router = $router;
    }

    /**
     * Wraps business logic with reusable boilerplate code.
     *
     * Handles form errors (NotificationHandler:warning).
     * Handles business logic exceptions (NotificationHandler:error).
     * Handles form submit redirection (success, failure) if Data object implements fitting interface.
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
                $result = call_user_func_array($handler, [$data]);

                if ($result instanceof Response) {
                    return $result;
                }

                if ($data instanceof OnSuccessRedirect) {
                    $onSuccess = $data->getOnSuccessRedirectionUrl();

                    if ($onSuccess) {
                        return $this->getRedirection($onSuccess, $result ?? []);
                    }
                }
            } catch (Exception $e) {
                $this->notificationHandler->error($e->getMessage());
            }
        } else {
            foreach ($form->getErrors(true, true) as $formError) {
                $this->notificationHandler->warning($formError->getMessage());
            }
        }

        if ($data instanceof OnFailureRedirect) {
            $onFailure = $data->getOnFailureRedirectionUrl();

            if ($onFailure) {
                return $this->getRedirection($onFailure);
            }
        }

        return null;
    }

    /**
     * Returns Redirection response based on type of parameter.
     *
     * If it's valid route, prepare URL with params first.
     * If it's not route, assume an URL.
     *
     * Useful for dynamic URLs based on the result of submit.
     * Params to generate route are returned from callable(mixed):array submit handler.
     *
     * @param string|null $field
     * @param array $params
     *
     * @return null|RedirectResponse
     */
    protected function getRedirection(string $field = null, $params = []): ?RedirectResponse
    {
        $route = $this->router->getRouteCollection()->get($field);

        $url = isset($route)
            ? $this->router->generate($field, $params)
            : $field;

        return new RedirectResponse($url);
    }
}
