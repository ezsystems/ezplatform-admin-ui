<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\RoleService;
use eZ\Publish\API\Repository\Values\User\Role;
use EzSystems\EzPlatformAdminUi\Form\Data\Role\RoleCreateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Role\RoleDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\Role\RolesDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\Role\RoleUpdateData;
use EzSystems\EzPlatformAdminUi\Form\DataMapper\RoleCreateMapper;
use EzSystems\EzPlatformAdminUi\Form\DataMapper\RoleUpdateMapper;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Form\SubmitHandler;
use EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\Translation\Exception\InvalidArgumentException;
use Symfony\Component\Translation\TranslatorInterface;

class RoleController extends Controller
{
    /** @var NotificationHandlerInterface */
    private $notificationHandler;

    /** @var TranslatorInterface */
    private $translator;

    /** @var RoleService */
    private $roleService;

    /** @var RoleCreateMapper */
    private $roleCreateMapper;

    /** @var RoleUpdateMapper */
    private $roleUpdateMapper;

    /** @var FormFactory */
    private $formFactory;

    /** @var SubmitHandler */
    private $submitHandler;

    /**
     * RoleController constructor.
     *
     * @param NotificationHandlerInterface $notificationHandler
     * @param TranslatorInterface $translator
     * @param RoleService $roleService
     * @param RoleCreateMapper $roleCreateMapper
     * @param RoleUpdateMapper $roleUpdateMapper
     * @param FormFactory $formFactory
     * @param SubmitHandler $submitHandler
     */
    public function __construct(
        NotificationHandlerInterface $notificationHandler,
        TranslatorInterface $translator,
        RoleService $roleService,
        RoleCreateMapper $roleCreateMapper,
        RoleUpdateMapper $roleUpdateMapper,
        FormFactory $formFactory,
        SubmitHandler $submitHandler
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->translator = $translator;
        $this->roleService = $roleService;
        $this->roleCreateMapper = $roleCreateMapper;
        $this->roleUpdateMapper = $roleUpdateMapper;
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
    }

    public function listAction(): Response
    {
        $roles = $this->roleService->loadRoles();

        $rolesNumbers = array_column($roles, 'id');

        $rolesDeleteData = new RolesDeleteData(
            array_combine($rolesNumbers, array_fill_keys($rolesNumbers, false))
        );

        $rolesDeleteForm = $this->formFactory->deleteRoles($rolesDeleteData);

        return $this->render('@EzPlatformAdminUi/admin/role/list.html.twig', [
            'form_roles_delete' => $rolesDeleteForm->createView(),
            'roles' => $roles,
        ]);
    }

    public function viewAction(Role $role): Response
    {
        $deleteForm = $this->formFactory->deleteRole(
            new RoleDeleteData($role)
        );

        $assignments = $this->roleService->getRoleAssignments($role);

        return $this->render('@EzPlatformAdminUi/admin/role/view.html.twig', [
            'role' => $role,
            'assignments' => $assignments,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    public function createAction(Request $request): Response
    {
        $form = $this->formFactory->createRole();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (RoleCreateData $data) {
                $roleCreateStruct = $this->roleCreateMapper->reverseMap($data);
                $roleDraft = $this->roleService->createRole($roleCreateStruct);
                $this->roleService->publishRoleDraft($roleDraft);

                $this->notificationHandler->success(
                    $this->translator->trans(
                        /** @Desc("Role '%role%' created.") */
                        'role.create.success',
                        ['%role%' => $roleDraft->identifier],
                        'role'
                    )
                );

                return new RedirectResponse($this->generateUrl('ezplatform.role.view', [
                    'roleId' => $roleDraft->id,
                ]));
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->render('@EzPlatformAdminUi/admin/role/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function updateAction(Request $request, Role $role): Response
    {
        $form = $this->formFactory->updateRole(
            new RoleUpdateData($role)
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (RoleUpdateData $data) {
                $role = $data->getRole();

                $roleUpdateStruct = $this->roleUpdateMapper->reverseMap($data);
                $roleDraft = $this->roleService->createRoleDraft($role);

                $this->roleService->updateRoleDraft($roleDraft, $roleUpdateStruct);
                $this->roleService->publishRoleDraft($roleDraft);

                $this->notificationHandler->success(
                    $this->translator->trans(
                        /** @Desc("Role '%role%' updated.") */
                        'role.update.success',
                        ['%role%' => $role->identifier],
                        'role'
                    )
                );

                return new RedirectResponse($this->generateUrl('ezplatform.role.view', [
                    'roleId' => $role->id,
                ]));
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->render('@EzPlatformAdminUi/admin/role/edit.html.twig', [
            'role' => $role,
            'form' => $form->createView(),
        ]);
    }

    public function deleteAction(Request $request, Role $role): Response
    {
        $form = $this->formFactory->deleteRole(
            new RoleDeleteData($role)
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (RoleDeleteData $data) {
                $role = $data->getRole();
                $this->roleService->deleteRole($role);

                $this->notificationHandler->success(
                    $this->translator->trans(
                        /** @Desc("Role '%role%' removed.") */
                        'role.delete.success',
                        ['%role%' => $role->identifier],
                        'role'
                    )
                );

                return new RedirectResponse($this->generateUrl('ezplatform.role.list'));
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirect($this->generateUrl('ezplatform.role.view', [
            'roleId' => $role->id,
        ]));
    }

    /**
     * Handles removing roles based on submitted form.
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws \InvalidArgumentException
     * @throws InvalidArgumentException
     * @throws UnauthorizedException
     * @throws InvalidOptionsException
     */
    public function bulkDeleteAction(Request $request): Response
    {
        $form = $this->formFactory->deleteRoles(
            new RolesDeleteData()
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (RolesDeleteData $data) {
                foreach ($data->getRoles() as $roleId => $selected) {
                    $role = $this->roleService->loadRole($roleId);
                    $this->roleService->deleteRole($role);

                    $this->notificationHandler->success(
                        $this->translator->trans(
                            /** @Desc("Role '%role%' removed.") */
                            'role.delete.success',
                            ['%role%' => $role->identifier],
                            'role'
                        )
                    );
                }

                return new RedirectResponse($this->generateUrl('ezplatform.role.list'));
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return new RedirectResponse($this->generateUrl('ezplatform.role.list'));
    }
}
