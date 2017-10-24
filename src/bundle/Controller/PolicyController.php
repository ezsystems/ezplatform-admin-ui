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
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Form\SubmitHandler;
use EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

class PolicyController extends Controller
{
    /** @var NotificationHandlerInterface */
    private $notificationHandler;

    /** @var TranslatorInterface */
    private $translator;

    /** @var RoleService */
    private $roleService;

    /** @var PolicyCreateMapper */
    private $policyCreateMapper;

    /** @var PolicyUpdateMapper */
    private $policyUpdateMapper;

    /** @var FormFactory */
    private $formFactory;

    /** @var SubmitHandler */
    private $submitHandler;

    /**
     * PolicyController constructor.
     *
     * @param NotificationHandlerInterface $notificationHandler
     * @param TranslatorInterface $translator
     * @param RoleService $roleService
     * @param PolicyCreateMapper $policyCreateMapper
     * @param PolicyUpdateMapper $policyUpdateMapper
     * @param FormFactory $formFactory
     * @param SubmitHandler $submitHandler
     */
    public function __construct(
        NotificationHandlerInterface $notificationHandler,
        TranslatorInterface $translator,
        RoleService $roleService,
        PolicyCreateMapper $policyCreateMapper,
        PolicyUpdateMapper $policyUpdateMapper,
        FormFactory $formFactory,
        SubmitHandler $submitHandler
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->translator = $translator;
        $this->roleService = $roleService;
        $this->policyCreateMapper = $policyCreateMapper;
        $this->policyUpdateMapper = $policyUpdateMapper;
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
    }

    public function listAction(Role $role): Response
    {
        $roleViewUrl = $this->generateUrl('ezplatform.role.view', ['roleId' => $role->id]);

        $policies = $role->getPolicies();

        $deleteFormsByPolicyId = [];
        foreach ($policies as $policy) {
            $deleteFormsByPolicyId[$policy->id] = $this->formFactory->deletePolicy(
                new PolicyDeleteData($policy),
                $roleViewUrl,
                $roleViewUrl
            )->createView();
        }

        return $this->render('@EzPlatformAdminUi/admin/policy/list.html.twig', [
            'deleteFormsByPolicyId' => $deleteFormsByPolicyId,
            'role' => $role,
        ]);
    }

    public function createAction(Request $request, Role $role): Response
    {
        $roleViewUrl = $this->generateUrl('ezplatform.role.view', ['roleId' => $role->id]);

        $form = $this->formFactory->createPolicy(
            new PolicyCreateData(),
            $roleViewUrl,
            $roleViewUrl
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (PolicyCreateData $data) use ($role) {
                $policyCreateStruct = $this->policyCreateMapper->reverseMap($data);

                $roleDraft = $this->roleService->createRoleDraft($role);
                $roleDraft = $this->roleService->addPolicyByRoleDraft($roleDraft, $policyCreateStruct);
                $this->roleService->publishRoleDraft($roleDraft);

                $this->notificationHandler->success(
                    $this->translator->trans(
                        /** @Desc("New policies in role '%role%' created.") */'policy.add.success',
                        ['%role%' => $role->identifier],
                        'role'
                    )
                );
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->render('@EzPlatformAdminUi/admin/policy/add.html.twig', [
            'role' => $role,
            'form' => $form->createView(),
        ]);
    }

    public function updateAction(Request $request, Role $role, Policy $policy): Response
    {
        $roleViewUrl = $this->generateUrl('ezplatform.role.view', ['roleId' => $role->id]);

        $form = $this->formFactory->updatePolicy(
            new PolicyUpdateData($policy),
            $roleViewUrl,
            $roleViewUrl
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (PolicyUpdateData $data) use ($role, $policy) {
                $policyUpdateStruct = $this->policyUpdateMapper->reverseMap($data);

                $roleDraft = $this->roleService->createRoleDraft($role);
                foreach ($roleDraft->getPolicies() as $policyDraft) {
                    if ($policyDraft->originalId == $policy->id) {
                        $this->roleService->updatePolicyByRoleDraft($roleDraft, $policyDraft, $policyUpdateStruct);
                        $this->roleService->publishRoleDraft($roleDraft);
                        break;
                    }
                }

                $this->notificationHandler->success(
                    $this->translator->trans(
                        /** @Desc("Policies in role '%role%' updated.") */'policy.update.success',
                        ['%role%' => $role->identifier],
                        'role'
                    )
                );
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->render('@EzPlatformAdminUi/admin/policy/edit.html.twig', [
            'role' => $role,
            'policy' => $policy,
            'form' => $form->createView(),
        ]);
    }

    public function deleteAction(Request $request, Role $role, Policy $policy): Response
    {
        $roleViewUrl = $this->generateUrl('ezplatform.role.view', ['roleId' => $role->id]);

        $form = $this->formFactory->deletePolicy(
            new PolicyDeleteData($policy),
            $roleViewUrl,
            $roleViewUrl
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (PolicyDeleteData $data) use ($role) {
                $roleDraft = $this->roleService->createRoleDraft($role);
                foreach ($roleDraft->getPolicies() as $policyDraft) {
                    if ($policyDraft->originalId == $data->getId()) {
                        $this->roleService->removePolicyByRoleDraft($roleDraft, $policyDraft);
                        $this->roleService->publishRoleDraft($roleDraft);
                        break;
                    }
                }

                $this->notificationHandler->success(
                    $this->translator->trans(
                        /** @Desc("Policies in role '%role%' removed.") */'policy.delete.success',
                        ['%role%' => $role->identifier],
                        'role'
                    )
                );
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        /* Fallback Redirect */
        return $this->redirect($roleViewUrl);
    }
}
