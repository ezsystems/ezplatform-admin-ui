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
use eZ\Publish\Core\MVC\Symfony\Security\Authorization\Attribute;
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
use Symfony\Component\Translation\TranslatorInterface;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;

class PolicyController extends Controller
{
    /** @var \EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface */
    private $notificationHandler;

    /** @var \Symfony\Component\Translation\TranslatorInterface */
    private $translator;

    /** @var \eZ\Publish\API\Repository\RoleService */
    private $roleService;

    /** @var \EzSystems\EzPlatformAdminUi\Form\DataMapper\PolicyCreateMapper */
    private $policyCreateMapper;

    /** @var \EzSystems\EzPlatformAdminUi\Form\DataMapper\PolicyUpdateMapper */
    private $policyUpdateMapper;

    /** @var \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory */
    private $formFactory;

    /** @var \EzSystems\EzPlatformAdminUi\Form\SubmitHandler */
    private $submitHandler;

    /** @var int */
    private $defaultPaginationLimit;

    /**
     * @param \EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface $notificationHandler
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     * @param \eZ\Publish\API\Repository\RoleService $roleService
     * @param \EzSystems\EzPlatformAdminUi\Form\DataMapper\PolicyCreateMapper $policyCreateMapper
     * @param \EzSystems\EzPlatformAdminUi\Form\DataMapper\PolicyUpdateMapper $policyUpdateMapper
     * @param \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory $formFactory
     * @param \EzSystems\EzPlatformAdminUi\Form\SubmitHandler $submitHandler
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

    /**
     * @param \eZ\Publish\API\Repository\Values\User\Role $role
     * @param string $routeName
     * @param int $policyPage
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \Pagerfanta\Exception\OutOfRangeCurrentPageException
     * @throws \Pagerfanta\Exception\NotIntegerCurrentPageException
     * @throws \Pagerfanta\Exception\LessThan1CurrentPageException
     * @throws \Pagerfanta\Exception\NotIntegerMaxPerPageException
     * @throws \Pagerfanta\Exception\LessThan1MaxPerPageException
     */
    public function listAction(Role $role, string $routeName, int $policyPage = 1): Response
    {
        $pagerfanta = new Pagerfanta(
            new ArrayAdapter($role->getPolicies())
        );

        $pagerfanta->setMaxPerPage($this->defaultPaginationLimit);
        $pagerfanta->setCurrentPage(min($policyPage, $pagerfanta->getNbPages()));

        /** @var \eZ\Publish\API\Repository\Values\User\Policy[] $policies */
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

        return $this->render('@ezdesign/admin/policy/list.html.twig', [
            'form_policies_delete' => $deletePoliciesForm->createView(),
            'is_editable' => $isEditable,
            'role' => $role,
            'pager' => $pagerfanta,
            'route_name' => $routeName,
            'can_update' => $this->isGranted(new Attribute('role', 'update')),
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \eZ\Publish\API\Repository\Values\User\Role $role
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Symfony\Component\Translation\Exception\InvalidArgumentException
     * @throws \InvalidArgumentException
     */
    public function createAction(Request $request, Role $role): Response
    {
        $this->denyAccessUnlessGranted(new Attribute('role', 'update'));
        $form = $this->formFactory->createPolicy(
            new PolicyCreateData()
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (PolicyCreateData $data) use ($role) {
                $policyCreateStruct = $this->policyCreateMapper->reverseMap($data);

                $limitationTypes = $policyCreateStruct->module
                    ? $this->roleService->getLimitationTypesByModuleFunction($policyCreateStruct->module, $policyCreateStruct->function)
                    : [];

                $isEditable = !empty($limitationTypes);

                if ($isEditable) {
                    $this->notificationHandler->success(
                        $this->translator->trans(
                            /** @Desc("Now you can set limitations for the policy.") */
                            'policy.add.set_limitation',
                            ['%role%' => $role->identifier],
                            'role'
                        )
                    );

                    return new RedirectResponse($this->generateUrl('ezplatform.policy.create_with_limitation', [
                        'roleId' => $role->id,
                        'policyModule' => $policyCreateStruct->module,
                        'policyFunction' => $policyCreateStruct->function,
                    ]));
                }

                try {
                    $this->roleService->deleteRoleDraft($this->roleService->loadRoleDraftByRoleId($role->id));
                } catch (NotFoundException $e) {
                }

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

        return $this->render('@ezdesign/admin/policy/add.html.twig', [
            'role' => $role,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \eZ\Publish\API\Repository\Values\User\Role $role
     * @param \eZ\Publish\API\Repository\Values\User\Policy $policy
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Symfony\Component\Translation\Exception\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \InvalidArgumentException
     */
    public function updateAction(Request $request, Role $role, Policy $policy): Response
    {
        $this->denyAccessUnlessGranted(new Attribute('role', 'update'));
        $limitationTypes = $policy->module
            ? $this->roleService->getLimitationTypesByModuleFunction($policy->module, $policy->function)
            : [];

        $isEditable = !empty($limitationTypes);

        if (!$isEditable) {
            $this->notificationHandler->error(
                $this->translator->trans(
                    /** @Desc("Policy type '%policy%' does not contain limitations.") */
                    'policy.edit.no_limitations',
                    ['%policy%' => $policy->module . '/' . $policy->function],
                    'role'
                )
            );

            return new RedirectResponse($this->generateUrl('ezplatform.role.view', [
                'roleId' => $role->id,
            ]));
        }

        $form = $this->formFactory->updatePolicy(
            new PolicyUpdateData($policy)
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (PolicyUpdateData $data) use ($role, $policy) {
                $policyUpdateStruct = $this->policyUpdateMapper->reverseMap($data);

                $roleDraft = $this->roleService->createRoleDraft($role);
                $policies = $roleDraft->getPolicies();
                foreach ($policies as $policyDraft) {
                    if ($policyDraft->originalId === $policy->id) {
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

        return $this->render('@ezdesign/admin/policy/edit.html.twig', [
            'role' => $role,
            'policy' => $policy,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \eZ\Publish\API\Repository\Values\User\Role $role
     * @param string $policyModule
     * @param string $policyFunction
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createWithLimitationAction(Request $request, Role $role, string $policyModule, string $policyFunction): Response
    {
        $this->denyAccessUnlessGranted(new Attribute('role', 'update'));
        $form = $this->formFactory->createPolicyWithLimitation(
            (new PolicyCreateData())->setPolicy([
                'module' => $policyModule,
                'function' => $policyFunction,
            ])
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

        return $this->render('@ezdesign/admin/policy/create_with_limitation.html.twig', [
            'role' => $role,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \eZ\Publish\API\Repository\Values\User\Role $role
     * @param \eZ\Publish\API\Repository\Values\User\Policy $policy
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Symfony\Component\Translation\Exception\InvalidArgumentException
     * @throws \InvalidArgumentException
     */
    public function deleteAction(Request $request, Role $role, Policy $policy): Response
    {
        $this->denyAccessUnlessGranted(new Attribute('role', 'update'));
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
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \eZ\Publish\API\Repository\Values\User\Role $role
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Symfony\Component\Translation\Exception\InvalidArgumentException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \InvalidArgumentException
     */
    public function bulkDeleteAction(Request $request, Role $role): Response
    {
        $this->denyAccessUnlessGranted(new Attribute('role', 'update'));
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
     * @param \eZ\Publish\API\Repository\Values\User\Policy[] $policies
     *
     * @return array
     */
    private function getPoliciesNumbers(array $policies): array
    {
        $policiesNumbers = array_column($policies, 'id');

        return array_combine($policiesNumbers, array_fill_keys($policiesNumbers, false));
    }
}
