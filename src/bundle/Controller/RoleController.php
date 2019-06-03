<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\RoleService;
use eZ\Publish\API\Repository\Values\User\Role;
use eZ\Publish\Core\MVC\Symfony\Security\Authorization\Attribute;
use EzSystems\EzPlatformAdminUi\Form\Data\Role\RoleCreateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Role\RoleDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\Role\RolesDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\Role\RoleUpdateData;
use EzSystems\EzPlatformAdminUi\Form\DataMapper\RoleCreateMapper;
use EzSystems\EzPlatformAdminUi\Form\DataMapper\RoleUpdateMapper;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Form\SubmitHandler;
use EzSystems\EzPlatformAdminUi\Notification\TranslatableNotificationHandlerInterface;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\Translation\Exception\InvalidArgumentException;

class RoleController extends Controller
{
    /** @var \EzSystems\EzPlatformAdminUi\Notification\TranslatableNotificationHandlerInterface */
    private $notificationHandler;

    /** @var \eZ\Publish\API\Repository\RoleService */
    private $roleService;

    /** @var \EzSystems\EzPlatformAdminUi\Form\DataMapper\RoleCreateMapper */
    private $roleCreateMapper;

    /** @var \EzSystems\EzPlatformAdminUi\Form\DataMapper\RoleUpdateMapper */
    private $roleUpdateMapper;

    /** @var \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory */
    private $formFactory;

    /** @var \EzSystems\EzPlatformAdminUi\Form\SubmitHandler */
    private $submitHandler;

    /** @var int */
    private $defaultPaginationLimit;

    /**
     * @param \EzSystems\EzPlatformAdminUi\Notification\TranslatableNotificationHandlerInterface $notificationHandler
     * @param \eZ\Publish\API\Repository\RoleService $roleService
     * @param \EzSystems\EzPlatformAdminUi\Form\DataMapper\RoleCreateMapper $roleCreateMapper
     * @param \EzSystems\EzPlatformAdminUi\Form\DataMapper\RoleUpdateMapper $roleUpdateMapper
     * @param \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory $formFactory
     * @param \EzSystems\EzPlatformAdminUi\Form\SubmitHandler $submitHandler
     * @param int $defaultPaginationLimit
     */
    public function __construct(
        TranslatableNotificationHandlerInterface $notificationHandler,
        RoleService $roleService,
        RoleCreateMapper $roleCreateMapper,
        RoleUpdateMapper $roleUpdateMapper,
        FormFactory $formFactory,
        SubmitHandler $submitHandler,
        int $defaultPaginationLimit
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->roleService = $roleService;
        $this->roleCreateMapper = $roleCreateMapper;
        $this->roleUpdateMapper = $roleUpdateMapper;
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
        $this->defaultPaginationLimit = $defaultPaginationLimit;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function listAction(Request $request): Response
    {
        $page = $request->query->get('page') ?? 1;

        $pagerfanta = new Pagerfanta(
            new ArrayAdapter($this->roleService->loadRoles())
        );

        $pagerfanta->setMaxPerPage($this->defaultPaginationLimit);
        $pagerfanta->setCurrentPage(min($page, $pagerfanta->getNbPages()));

        /** @var \eZ\Publish\API\Repository\Values\User\Role[] $sectionList */
        $roles = $pagerfanta->getCurrentPageResults();

        $rolesNumbers = array_column($roles, 'id');

        $rolesDeleteData = new RolesDeleteData(
            array_combine($rolesNumbers, array_fill_keys($rolesNumbers, false))
        );

        $rolesDeleteForm = $this->formFactory->deleteRoles($rolesDeleteData);

        return $this->render('@ezdesign/user/role/list.html.twig', [
            'form_roles_delete' => $rolesDeleteForm->createView(),
            'pager' => $pagerfanta,
            'can_create' => $this->isGranted(new Attribute('role', 'create')),
            'can_delete' => $this->isGranted(new Attribute('role', 'delete')),
            'can_update' => $this->isGranted(new Attribute('role', 'update')),
            'can_assign' => $this->isGranted(new Attribute('role', 'assign')),
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \eZ\Publish\API\Repository\Values\User\Role $role
     * @param int $policyPage
     * @param int $assignmentPage
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction(Request $request, Role $role, int $policyPage = 1, int $assignmentPage = 1): Response
    {
        $deleteForm = $this->formFactory->deleteRole(
            new RoleDeleteData($role)
        );

        // If user has no permission to content/read than he should see empty table.
        try {
            $assignments = $this->roleService->getRoleAssignments($role);
        } catch (UnauthorizedException $e) {
            $assignments = [];
        }

        return $this->render('@ezdesign/user/role/index.html.twig', [
            'role' => $role,
            'assignments' => $assignments,
            'delete_form' => $deleteForm->createView(),
            'route_name' => $request->get('_route'),
            'policy_page' => $policyPage,
            'assignment_page' => $assignmentPage,
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request): Response
    {
        $this->denyAccessUnlessGranted(new Attribute('role', 'create'));
        $form = $this->formFactory->createRole();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (RoleCreateData $data) {
                $roleCreateStruct = $this->roleCreateMapper->reverseMap($data);
                $roleDraft = $this->roleService->createRole($roleCreateStruct);
                $this->roleService->publishRoleDraft($roleDraft);

                $this->notificationHandler->success(
                    /** @Desc("Role '%role%' created.") */
                    'role.create.success',
                    ['%role%' => $roleDraft->identifier],
                    'role'
                );

                return new RedirectResponse($this->generateUrl('ezplatform.role.view', [
                    'roleId' => $roleDraft->id,
                ]));
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->render('@ezdesign/user/role/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \eZ\Publish\API\Repository\Values\User\Role $role
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request, Role $role): Response
    {
        $this->denyAccessUnlessGranted(new Attribute('role', 'update'));
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
                    /** @Desc("Role '%role%' updated.") */
                    'role.update.success',
                    ['%role%' => $role->identifier],
                    'role'
                );

                return new RedirectResponse($this->generateUrl('ezplatform.role.view', [
                    'roleId' => $role->id,
                ]));
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->render('@ezdesign/user/role/edit.html.twig', [
            'role' => $role,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \eZ\Publish\API\Repository\Values\User\Role $role
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Request $request, Role $role): Response
    {
        $this->denyAccessUnlessGranted(new Attribute('role', 'delete'));
        $form = $this->formFactory->deleteRole(
            new RoleDeleteData($role)
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (RoleDeleteData $data) {
                $role = $data->getRole();
                $this->roleService->deleteRole($role);

                $this->notificationHandler->success(
                    /** @Desc("Role '%role%' removed.") */
                    'role.delete.success',
                    ['%role%' => $role->identifier],
                    'role'
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
     * @throws InvalidOptionsException
     */
    public function bulkDeleteAction(Request $request): Response
    {
        $this->denyAccessUnlessGranted(new Attribute('role', 'delete'));
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
                        /** @Desc("Role '%role%' removed.") */
                        'role.delete.success',
                        ['%role%' => $role->identifier],
                        'role'
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
