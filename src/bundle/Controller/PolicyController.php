<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\Exceptions\InvalidArgumentException;
use eZ\Publish\API\Repository\Exceptions\LimitationValidationException;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\API\Repository\RoleService;
use eZ\Publish\API\Repository\Values\User\Policy;
use eZ\Publish\API\Repository\Values\User\Role;
use EzSystems\EzPlatformAdminUi\Form\Data\Policy\PoliciesDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\Policy\PolicyCreateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Policy\PolicyDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\Policy\PolicyUpdateData;
use EzSystems\EzPlatformAdminUi\Form\DataMapper\PolicyCreateMapper;
use EzSystems\EzPlatformAdminUi\Form\DataMapper\PolicyUpdateMapper;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Form\SubmitHandler;
use EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\Translation\Exception\InvalidArgumentException as TranslationInvalidArgumentException;
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

    /** @var int */
    private $defaultPaginationLimit;

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
     * @param int $defaultPaginationLimit
     */
    public function __construct(
        NotificationHandlerInterface $notificationHandler,
        TranslatorInterface $translator,
        RoleService $roleService,
        PolicyCreateMapper $policyCreateMapper,
        PolicyUpdateMapper $policyUpdateMapper,
        FormFactory $formFactory,
        SubmitHandler $submitHandler,
        int $defaultPaginationLimit
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->translator = $translator;
        $this->roleService = $roleService;
        $this->policyCreateMapper = $policyCreateMapper;
        $this->policyUpdateMapper = $policyUpdateMapper;
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
        $this->defaultPaginationLimit = $defaultPaginationLimit;
    }

    public function listAction(Role $role, string $routeName, int $policyPage = 1): Response
    {
        $pagerfanta = new Pagerfanta(
            new ArrayAdapter($role->getPolicies())
        );

        $pagerfanta->setMaxPerPage($this->defaultPaginationLimit);
        $pagerfanta->setCurrentPage(min($policyPage, $pagerfanta->getNbPages()));

        /** @var Policy[] $policies */
        $policies = $pagerfanta->getCurrentPageResults();

        $isEditable = [];
        foreach ($policies as $policy) {
            $limitationTypes = $policy->module
                ? $this->roleService->getLimitationTypesByModuleFunction($policy->module, $policy->function)
                : [];

            $isEditable[$policy->id] = !empty($limitationTypes);
        }

        $deletePoliciesForm = $this->formFactory->deletePolicies(
            new PoliciesDeleteData($role, $this->getPoliciesNumbers($policies))
        );

        return $this->render('@EzPlatformAdminUi/admin/policy/list.html.twig', [
            'form_policies_delete' => $deletePoliciesForm->createView(),
            'is_editable' => $isEditable,
            'role' => $role,
            'pager' => $pagerfanta,
            'route_name' => $routeName,
        ]);
    }

    public function createAction(Request $request, Role $role): Response
    {
        $form = $this->formFactory->createPolicy(
            new PolicyCreateData()
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
                        /** @Desc("New policies in role '%role%' created.") */
                        'policy.add.success',
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

        return $this->render('@EzPlatformAdminUi/admin/policy/add.html.twig', [
            'role' => $role,
            'form' => $form->createView(),
        ]);
    }

    public function updateAction(Request $request, Role $role, Policy $policy): Response
    {
        $form = $this->formFactory->updatePolicy(
            new PolicyUpdateData($policy)
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
                        /** @Desc("Policies in role '%role%' updated.") */
                        'policy.update.success',
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

        return $this->render('@EzPlatformAdminUi/admin/policy/edit.html.twig', [
            'role' => $role,
            'policy' => $policy,
            'form' => $form->createView(),
        ]);
    }

    public function deleteAction(Request $request, Role $role, Policy $policy): Response
    {
        $form = $this->formFactory->deletePolicy(
            new PolicyDeleteData($policy)
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
                        /** @Desc("Policies in role '%role%' removed.") */
                        'policy.delete.success',
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

        return $this->redirect($this->generateUrl('ezplatform.role.view', [
            'roleId' => $role->id,
        ]));
    }

    /**
     * Handles removing policies based on submitted form.
     *
     * @param Request $request
     * @param Role $role
     *
     * @return Response
     *
     * @throws TranslationInvalidArgumentException
     * @throws UnauthorizedException
     * @throws LimitationValidationException
     * @throws InvalidOptionsException
     * @throws InvalidArgumentException
     * @throws \InvalidArgumentException
     */
    public function bulkDeleteAction(Request $request, Role $role): Response
    {
        $form = $this->formFactory->deletePolicies(
            new PoliciesDeleteData()
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $result = $this->submitHandler->handle($form, function (PoliciesDeleteData $data) use ($role) {
                $roleDraft = $this->roleService->createRoleDraft($role);

                foreach ($data->getPolicies() as $policyId => $selected) {
                    foreach ($roleDraft->getPolicies() as $policyDraft) {
                        if ($policyDraft->originalId === $policyId) {
                            $this->roleService->removePolicyByRoleDraft($roleDraft, $policyDraft);
                        }
                    }
                }

                $this->roleService->publishRoleDraft($roleDraft);

                $this->notificationHandler->success(
                    $this->translator->trans(
                        /** @Desc("Policies in role '%role%' removed.") */
                        'policy.delete.success',
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

        return $this->redirect($this->generateUrl('ezplatform.role.view', [
            'roleId' => $role->id,
        ]));
    }

    /**
     * @param Policy[] $policies
     *
     * @return array
     */
    private function getPoliciesNumbers(array $policies): array
    {
        $policiesNumbers = array_column($policies, 'id');

        return array_combine($policiesNumbers, array_fill_keys($policiesNumbers, false));
    }
}
