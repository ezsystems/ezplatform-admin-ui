<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\RoleService;
use eZ\Publish\API\Repository\Values\User\Policy;
use eZ\Publish\API\Repository\Values\User\Role;
use EzSystems\EzPlatformAdminUi\Form\Data\Policy\PolicyCreateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Policy\PolicyDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\Policy\PolicyUpdateData;
use EzSystems\EzPlatformAdminUi\Form\DataMapper\PolicyCreateMapper;
use EzSystems\EzPlatformAdminUi\Form\DataMapper\PolicyUpdateMapper;
use EzSystems\EzPlatformAdminUi\Form\Type\Policy\PolicyCreateType;
use EzSystems\EzPlatformAdminUi\Form\Type\Policy\PolicyDeleteType;
use EzSystems\EzPlatformAdminUi\Form\Type\Policy\PolicyUpdateType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PolicyController extends Controller
{
    /** @var RoleService */
    private $roleService;

    /** @var PolicyCreateMapper */
    private $policyCreateMapper;

    /** @var PolicyUpdateMapper */
    private $policyUpdateMapper;

    /**
     * PolicyController constructor.
     *
     * @param RoleService $roleService
     * @param PolicyCreateMapper $policyCreateMapper
     * @param PolicyUpdateMapper $policyUpdateMapper
     */
    public function __construct(
        RoleService $roleService,
        PolicyCreateMapper $policyCreateMapper,
        PolicyUpdateMapper $policyUpdateMapper
    ) {
        $this->roleService = $roleService;
        $this->policyCreateMapper = $policyCreateMapper;
        $this->policyUpdateMapper = $policyUpdateMapper;
    }

    public function listAction(Role $role): Response
    {
        $policies = $role->getPolicies();

        $deleteFormsByPolicyId = [];
        foreach ($policies as $policy) {
            $deleteFormsByPolicyId[$policy->id] = $this->createForm(
                PolicyDeleteType::class,
                new PolicyDeleteData($policy)
            )->createView();
        }

        return $this->render('@EzPlatformAdminUi/admin/policy/list.html.twig', [
            'deleteFormsByPolicyId' => $deleteFormsByPolicyId,
            'role' => $role,
        ]);
    }

    public function createAction(Request $request, Role $role): Response
    {
        $form = $this->createForm(PolicyCreateType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var PolicyCreateData $data */
            $data = $form->getData();
            $policyCreateStruct = $this->policyCreateMapper->reverseMap($data);

            $roleDraft = $this->roleService->createRoleDraft($role);
            $roleDraft = $this->roleService->addPolicyByRoleDraft($roleDraft, $policyCreateStruct);
            $this->roleService->publishRoleDraft($roleDraft);

            $this->addFlash('sucess', 'policy.added');

            return $this->redirect($this->generateUrl('ezplatform.role.view', ['roleId' => $role->id]));
        }

        foreach ($form->getErrors(true, true) as $formError) {
            $this->addFlash('danger', $formError->getMessage());
        }

        return $this->render('@EzPlatformAdminUi/admin/policy/add.html.twig', [
            'role' => $role,
            'form' => $form->createView(),
        ]);
    }

    public function updateAction(Request $request, Role $role, Policy $policy): Response
    {
        $form = $this->createForm(PolicyUpdateType::class, new PolicyUpdateData($policy));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var PolicyUpdateData $data */
            $data = $form->getData();
            $policyUpdateStruct = $this->policyUpdateMapper->reverseMap($data);

            $roleDraft = $this->roleService->createRoleDraft($role);
            foreach ($roleDraft->getPolicies() as $policyDraft) {
                if ($policyDraft->originalId == $policy->id) {
                    $this->roleService->updatePolicyByRoleDraft($roleDraft, $policyDraft, $policyUpdateStruct);
                    $this->roleService->publishRoleDraft($roleDraft);
                    break;
                }
            }

            $this->addFlash('success', 'policy.updated');

            return $this->redirect($this->generateUrl('ezplatform.role.view', ['roleId' => $role->id]));
        }

        foreach ($form->getErrors(true, true) as $formError) {
            $this->addFlash('danger', $formError->getMessage());
        }

        return $this->render('@EzPlatformAdminUi/admin/policy/edit.html.twig', [
            'role' => $role,
            'form' => $form->createView(),
        ]);
    }

    public function deleteAction(Request $request, Role $role, Policy $policy): Response
    {
        $form = $this->createForm(PolicyDeleteType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var PolicyDeleteData $data */
            $data = $form->getData();

            $roleDraft = $this->roleService->createRoleDraft($role);
            foreach ($roleDraft->getPolicies() as $policyDraft) {
                if ($policyDraft->originalId == $policy->id) {
                    $this->roleService->removePolicyByRoleDraft($roleDraft, $policyDraft);
                    $this->roleService->publishRoleDraft($roleDraft);
                    break;
                }
            }

            $this->addFlash('success', 'policy.deleted');
        }

        foreach ($form->getErrors(true, true) as $formError) {
            $this->addFlash('danger', $formError->getMessage());
        }

        return $this->redirect($this->generateUrl('ezplatform.role.view', ['roleId' => $role->id]));
    }
}
