<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\API\Repository\RoleService;
use eZ\Publish\API\Repository\Values\User\Limitation\RoleLimitation;
use eZ\Publish\API\Repository\Values\User\Limitation\SectionLimitation;
use eZ\Publish\API\Repository\Values\User\Limitation\SubtreeLimitation;
use eZ\Publish\API\Repository\Values\User\Role;
use eZ\Publish\API\Repository\Values\User\RoleAssignment;
use EzSystems\EzPlatformAdminUi\Form\Data\Role\RoleAssignmentsDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\Role\RoleAssignmentCreateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Role\RoleAssignmentDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Form\SubmitHandler;
use EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\Translation\Exception\InvalidArgumentException;
use Symfony\Component\Translation\TranslatorInterface;

class RoleAssignmentController extends Controller
{
    /** @var NotificationHandlerInterface */
    private $notificationHandler;

    /** @var TranslatorInterface */
    private $translator;

    /** @var RoleService */
    private $roleService;

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
     * @param FormFactory $formFactory
     * @param SubmitHandler $submitHandler
     * @param int $defaultPaginationLimit
     */
    public function __construct(
        NotificationHandlerInterface $notificationHandler,
        TranslatorInterface $translator,
        RoleService $roleService,
        FormFactory $formFactory,
        SubmitHandler $submitHandler,
        int $defaultPaginationLimit
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->translator = $translator;
        $this->roleService = $roleService;
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
        $this->defaultPaginationLimit = $defaultPaginationLimit;
    }

    /**
     * @param Role $role
     * @param string $routeName
     * @param int $assignmentPage
     *
     * @return Response
     */
    public function listAction(Role $role, string $routeName, int $assignmentPage = 1): Response
    {
        $pagerfanta = new Pagerfanta(
            new ArrayAdapter($this->roleService->getRoleAssignments($role))
        );

        $pagerfanta->setMaxPerPage($this->defaultPaginationLimit);
        $pagerfanta->setCurrentPage(min($assignmentPage, $pagerfanta->getNbPages()));

        /** @var RoleAssignment[] $assignments */
        $assignments = $pagerfanta->getCurrentPageResults();

        $deleteRoleAssignmentsForm = $this->formFactory->deleteRoleAssignments(
            new RoleAssignmentsDeleteData($role, $this->getRoleAssignmentsNumbers($assignments))
        );

        return $this->render('@ezdesign/admin/role_assignment/list.html.twig', [
            'role' => $role,
            'form_role_assignments_delete' => $deleteRoleAssignmentsForm->createView(),
            'pager' => $pagerfanta,
            'route_name' => $routeName,
        ]);
    }

    public function createAction(Request $request, Role $role): Response
    {
        $form = $this->formFactory->createRoleAssignment(new RoleAssignmentCreateData());
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (RoleAssignmentCreateData $data) use ($role) {
                foreach ($this->createLimitations($data) as $limitation) {
                    foreach ($data->getUsers() as $user) {
                        $this->roleService->assignRoleToUser($role, $user, $limitation);
                    }
                    foreach ($data->getGroups() as $group) {
                        $this->roleService->assignRoleToUserGroup($role, $group, $limitation);
                    }
                }

                $this->notificationHandler->success(
                    $this->translator->trans(
                        /** @Desc("Assignments on role '%role%' created.") */
                        'role.assignment_create.success',
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

        return $this->render('@ezdesign/admin/role_assignment/create.html.twig', [
            'role' => $role,
            'form' => $form->createView(),
        ]);
    }

    public function deleteAction(Request $request, Role $role, RoleAssignment $roleAssignment): Response
    {
        $form = $this->formFactory->deleteRoleAssignment(
            new RoleAssignmentDeleteData($roleAssignment)
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $result = $this->submitHandler->handle($form, function (RoleAssignmentDeleteData $data) use ($role) {
                $roleAssignment = $data->getRoleAssignment();
                $this->roleService->removeRoleAssignment($roleAssignment);

                $this->notificationHandler->success(
                    $this->translator->trans(
                        /** @Desc("Assignment on role '%role%' removed.") */
                        'role.assignment_delete.success',
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
     * Handles removing role assignments based on submitted form.
     *
     * @param Request $request
     * @param Role $role
     *
     * @return Response
     *
     * @throws \InvalidArgumentException
     * @throws InvalidArgumentException
     * @throws UnauthorizedException
     * @throws NotFoundException
     * @throws InvalidOptionsException
     */
    public function bulkDeleteAction(Request $request, Role $role): Response
    {
        $form = $this->formFactory->deleteRoleAssignments(
            new RoleAssignmentsDeleteData()
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $result = $this->submitHandler->handle($form, function (RoleAssignmentsDeleteData $data) use ($role) {
                foreach ($data->getRoleAssignments() as $roleAssignmentId => $selected) {
                    $roleAssignment = $this->roleService->loadRoleAssignment($roleAssignmentId);
                    $this->roleService->removeRoleAssignment($roleAssignment);
                }

                $this->notificationHandler->success(
                    $this->translator->trans(
                        /** @Desc("Assignment on role '%role%' removed.") */
                        'role.assignment_delete.success',
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
     * @param RoleAssignment[] $roleAssignments
     *
     * @return array
     */
    private function getRoleAssignmentsNumbers(array $roleAssignments): array
    {
        $roleAssignmentsNumbers = array_column($roleAssignments, 'id');

        return array_combine($roleAssignmentsNumbers, array_fill_keys($roleAssignmentsNumbers, false));
    }

    /**
     * @param RoleAssignmentCreateData $data
     *
     * @return RoleLimitation[]
     */
    private function createLimitations(RoleAssignmentCreateData $data): array
    {
        $limitations = [];
        switch ($data->getLimitationType()) {
            case RoleAssignmentCreateData::LIMITATION_TYPE_LOCATION:
                $limitation = new SubtreeLimitation();

                foreach ($data->getLocations() as $location) {
                    $limitation->limitationValues[] = $location->pathString;
                }

                $limitations[] = $limitation;
                break;
            case RoleAssignmentCreateData::LIMITATION_TYPE_SECTION:
                $limitation = new SectionLimitation();

                foreach ($data->getSections() as $section) {
                    $limitation->limitationValues[] = $section->id;
                }

                $limitations[] = $limitation;
                break;
            case RoleAssignmentCreateData::LIMITATION_TYPE_NONE:
                $limitations[] = null; // this acts as "no limitations"
                break;
        }

        return $limitations;
    }
}
