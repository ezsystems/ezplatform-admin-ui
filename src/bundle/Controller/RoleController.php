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
use EzSystems\EzPlatformAdminUi\Form\Data\Role\RoleUpdateData;
use EzSystems\EzPlatformAdminUi\Form\DataMapper\RoleCreateMapper;
use EzSystems\EzPlatformAdminUi\Form\DataMapper\RoleUpdateMapper;
use EzSystems\EzPlatformAdminUi\Form\Type\Role\RoleCreateType;
use EzSystems\EzPlatformAdminUi\Form\Type\Role\RoleDeleteType;
use EzSystems\EzPlatformAdminUi\Form\Type\Role\RoleUpdateType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleController extends Controller
{
    /** @var RoleService */
    private $roleService;

    /** @var RoleCreateMapper */
    private $roleCreateMapper;

    /** @var RoleUpdateMapper */
    private $roleUpdateMapper;

    /**
     * RoleController constructor.
     *
     * @param RoleService $roleService
     * @param RoleCreateMapper $roleCreateMapper
     * @param RoleUpdateMapper $roleUpdateMapper
     */
    public function __construct(
        RoleService $roleService,
        RoleCreateMapper $roleCreateMapper,
        RoleUpdateMapper $roleUpdateMapper
    ) {
        $this->roleService = $roleService;
        $this->roleCreateMapper = $roleCreateMapper;
        $this->roleUpdateMapper = $roleUpdateMapper;
    }

    public function listAction(): Response
    {
        $roles = $this->roleService->loadRoles();

        return $this->render('@EzPlatformAdminUi/admin/role/list.html.twig', [
            'roles' => $roles,
        ]);
    }

    public function viewAction(Role $role): Response
    {
        $deleteForm = $this->createForm(RoleDeleteType::class, new RoleDeleteData($role));

        return $this->render('@EzPlatformAdminUi/admin/role/view.html.twig', [
            'role' => $role,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    public function createAction(Request $request): Response
    {
        $form = $this->createForm(RoleCreateType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var RoleCreateData $data */
            $data = $form->getData();

            $roleCreateStruct = $this->roleCreateMapper->reverseMap($data);
            $roleDraft = $this->roleService->createRole($roleCreateStruct);
            $this->roleService->publishRoleDraft($roleDraft);

            $this->addFlash('success', 'role.created');

            return $this->redirect($this->generateUrl('ezplatform.role.view', ['roleId' => $roleDraft->id]));
        }

        foreach ($form->getErrors(true, true) as $formError) {
            $this->addFlash('danger', $formError->getMessage());
        }

        return $this->render('@EzPlatformAdminUi/admin/role/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function updateAction(Request $request, Role $role): Response
    {
        $form = $this->createForm(RoleUpdateType::class, new RoleUpdateData($role));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var RoleUpdateData $data */
            $data = $form->getData();

            $roleUpdateStruct = $this->roleUpdateMapper->reverseMap($data);
            $roleDraft = $this->roleService->createRoleDraft($role);
            $this->roleService->updateRoleDraft($roleDraft, $roleUpdateStruct);
            $this->roleService->publishRoleDraft($roleDraft);

            $this->flashSuccess('role.updated', [], 'role');

            return $this->redirect($this->generateUrl('ezplatform.role.view', [
                'roleId' => $role->id,
            ]));
        }

        foreach ($form->getErrors(true, true) as $formError) {
            $this->addFlash('danger', $formError->getMessage());
        }

        return $this->render('@EzPlatformAdminUi/admin/role/edit.html.twig', [
            'role' => $role,
            'form' => $form->createView(),
        ]);
    }

    public function deleteAction(Request $request, Role $role): Response
    {
        $form = $this->createForm(RoleDeleteType::class, new RoleDeleteData($role));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->roleService->deleteRole($role);

            $this->addFlash('success', 'role.deleted');

            return $this->redirect($this->generateUrl('ezplatform.role.list'));
        }

        foreach ($form->getErrors(true, true) as $formError) {
            $this->addFlash('danger', $formError->getMessage());
        }

        return $this->redirect($this->generateUrl('ezplatform.role.view', ['roleId' => $role->id]));
    }
}
