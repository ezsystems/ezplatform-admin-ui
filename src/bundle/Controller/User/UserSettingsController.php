<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Controller\User;

use EzSystems\EzPlatformAdminUi\Form\Data\User\Setting\UserSettingUpdateData;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Form\SubmitHandler;
use EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface;
use EzSystems\EzPlatformAdminUi\Pagination\Pagerfanta\UserSettingsAdapter;
use EzSystems\EzPlatformAdminUi\UserSetting\UserSettingService;
use EzSystems\EzPlatformAdminUi\UserSetting\ValueDefinitionRegistry;
use EzSystems\EzPlatformAdminUiBundle\Controller\Controller;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

class UserSettingsController extends Controller
{
    /** @var \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory */
    private $formFactory;

    /** @var \EzSystems\EzPlatformAdminUi\Form\SubmitHandler */
    private $submitHandler;

    /** @var \Symfony\Component\Translation\TranslatorInterface */
    private $translator;

    /** @var \EzSystems\EzPlatformAdminUi\UserSetting\UserSettingService */
    private $userSettingService;

    /** @var \EzSystems\EzPlatformAdminUi\UserSetting\ValueDefinitionRegistry */
    private $valueDefinitionRegistry;

    /** @var \EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface */
    private $notificationHandler;

    /** @var int */
    private $defaultPaginationLimit;

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory $formFactory
     * @param \EzSystems\EzPlatformAdminUi\Form\SubmitHandler $submitHandler
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     * @param \EzSystems\EzPlatformAdminUi\UserSetting\UserSettingService $userSettingService
     * @param \EzSystems\EzPlatformAdminUi\UserSetting\ValueDefinitionRegistry $valueDefinitionRegistry
     * @param \EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface $notificationHandler
     * @param int $defaultPaginationLimit
     */
    public function __construct(
        FormFactory $formFactory,
        SubmitHandler $submitHandler,
        TranslatorInterface $translator,
        UserSettingService $userSettingService,
        ValueDefinitionRegistry $valueDefinitionRegistry,
        NotificationHandlerInterface $notificationHandler,
        int $defaultPaginationLimit
    ) {
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
        $this->translator = $translator;
        $this->userSettingService = $userSettingService;
        $this->valueDefinitionRegistry = $valueDefinitionRegistry;
        $this->notificationHandler = $notificationHandler;
        $this->defaultPaginationLimit = $defaultPaginationLimit;
    }

    /**
     * @param int $page
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(int $page = 1): Response
    {
        $pagerfanta = new Pagerfanta(
            new UserSettingsAdapter($this->userSettingService)
        );

        $pagerfanta->setMaxPerPage($this->defaultPaginationLimit);
        $pagerfanta->setCurrentPage(min($page, $pagerfanta->getNbPages()));

        return $this->render('@ezdesign/user/settings/list.html.twig', [
            'pager' => $pagerfanta,
            'value_definitions' => $this->valueDefinitionRegistry->getValueDefinitions(),
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $identifier
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function updateAction(Request $request, string $identifier): Response
    {
        $userSetting = $this->userSettingService->getUserSetting($identifier);
        $data = new UserSettingUpdateData($identifier, $userSetting->value);

        $form = $this->formFactory->updateUserSetting($identifier, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (UserSettingUpdateData $data) {
                $this->userSettingService->setUserSetting($data->getIdentifier(), $data->getValue());

                $this->notificationHandler->success(
                    $this->translator->trans(
                        /** @Desc("User setting '%identifier%' updated.") */
                        'user_setting.update.success',
                        ['%identifier%' => $data->getIdentifier()],
                        'user_settings'
                    )
                );

                return new RedirectResponse($this->generateUrl('ezplatform.user_settings.list'));
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->render('@ezdesign/user/settings/update.html.twig', [
            'form' => $form->createView(),
            'user_setting' => $userSetting,
        ]);
    }
}
