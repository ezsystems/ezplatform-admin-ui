<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\API\Repository\RoleService;
use eZ\Publish\API\Repository\Values\User\Limitation\SectionLimitation;
use eZ\Publish\API\Repository\Values\User\Limitation\SubtreeLimitation;
use eZ\Publish\API\Repository\Values\User\Role;
use eZ\Publish\API\Repository\Values\User\RoleAssignment;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\Core\MVC\Symfony\Security\Authorization\Attribute;
use EzSystems\EzPlatformAdminUi\Form\Data\Role\RoleAssignmentCreateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Role\RoleAssignmentDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\Role\RoleAssignmentsDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Form\SubmitHandler;
use EzSystems\EzPlatformAdminUi\Notification\TranslatableNotificationHandlerInterface;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleAssignmentController extends Controller
{
    /** @var \EzSystems\EzPlatformAdminUi\Notification\TranslatableNotificationHandlerInterface */
    private $notificationHandler;

    /** @var \eZ\Publish\API\Repository\RoleService */
    private $roleService;

    /** @var \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory */
    private $formFactory;

    /** @var \EzSystems\EzPlatformAdminUi\Form\SubmitHandler */
    private $submitHandler;

    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    public function __construct(
        TranslatableNotificationHandlerInterface $notificationHandler,
        RoleService $roleService,
        FormFactory $formFactory,
        SubmitHandler $submitHandler,
        ConfigResolverInterface $configResolver
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->roleService = $roleService;
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
        $this->configResolver = $configResolver;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\User\Role $role
     * @param string $routeName
     * @param int $assignmentPage
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Role $role, string $routeName, int $assignmentPage = 1): Response
    {
        // If user has no permission to content/read than he should see empty table.
        try {
            $assignments = $this->roleService->getRoleAssignments($role);
        } catch (UnauthorizedException $e) {
            $assignments = [];
        }

        $pagerfanta = new Pagerfanta(
            new ArrayAdapter($assignments)
        );

        $pagerfanta->setMaxPerPage($this->configResolver->getParameter('pagination.role_assignment_limit'));
        $pagerfanta->setCurrentPage(min($assignmentPage, $pagerfanta->getNbPages()));

        /** @var \eZ\Publish\API\Repository\Values\User\RoleAssignment[] $assignments */
        $assignments = $pagerfanta->getCurrentPageResults();

        $deleteRoleAssignmentsForm = $this->formFactory->deleteRoleAssignments(
            new RoleAssignmentsDeleteData($role, $this->getRoleAssignmentsNumbers($assignments))
        );

        return $this->render('@ezdesign/user/role_assignment/list.html.twig', [
            'role' => $role,
            'form_role_assignments_delete' => $deleteRoleAssignmentsForm->createView(),
            'pager' => $pagerfanta,
            'route_name' => $routeName,
            'can_assign' => $this->isGranted(new Attribute('role', 'assign')),
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \eZ\Publish\API\Repository\Values\User\Role $role
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request, Role $role): Response
    {
        $this->denyAccessUnlessGranted(new Attribute('role', 'assign'));
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
                    /** @Desc("Assigned Users/Groups to Role '%role%'.") */
                    'role.assignment_create.success',
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

        return $this->render('@ezdesign/user/role_assignment/create.html.twig', [
            'role' => $role,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \eZ\Publish\API\Repository\Values\User\Role $role
     * @param \eZ\Publish\API\Repository\Values\User\RoleAssignment $roleAssignment
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Request $request, Role $role, RoleAssignment $roleAssignment): Response
    {
        $this->denyAccessUnlessGranted(new Attribute('role', 'assign'));
        $form = $this->formFactory->deleteRoleAssignment(
            new RoleAssignmentDeleteData($roleAssignment)
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (RoleAssignmentDeleteData $data) use ($role) {
                $roleAssignment = $data->getRoleAssignment();
                $this->roleService->removeRoleAssignment($roleAssignment);

                $this->notificationHandler->success(
                    /** @Desc("Unassigned Users/Groups from Role '%role%'.") */
                    'role.assignment_delete.success',
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

        return $this->redirect($this->generateUrl('ezplatform.role.view', [
            'roleId' => $role->id,
        ]));
    }

    /**
     * Handles removing role assignments based on submitted form.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \eZ\Publish\API\Repository\Values\User\Role $role
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function bulkDeleteAction(Request $request, Role $role): Response
    {
        $this->denyAccessUnlessGranted(new Attribute('role', 'assign'));
        $form = $this->formFactory->deleteRoleAssignments(
            new RoleAssignmentsDeleteData()
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (RoleAssignmentsDeleteData $data) use ($role) {
                foreach ($data->getRoleAssignments() as $roleAssignmentId => $selected) {
                    $roleAssignment = $this->roleService->loadRoleAssignment($roleAssignmentId);
                    $this->roleService->removeRoleAssignment($roleAssignment);
                }

                $this->notificationHandler->success(
                    /** @Desc("Unassigned Users/Groups from Role '%role%'.") */
                    'role.assignment_delete.success',
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

        return $this->redirect($this->generateUrl('ezplatform.role.view', [
            'roleId' => $role->id,
        ]));
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\User\RoleAssignment[] $roleAssignments
     *
     * @return array
     */
    private function getRoleAssignmentsNumbers(array $roleAssignments): array
    {
        $roleAssignmentsNumbers = array_column($roleAssignments, 'id');

        return array_combine($roleAssignmentsNumbers, array_fill_keys($roleAssignmentsNumbers, false));
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\Role\RoleAssignmentCreateData $data
     *
     * @return \eZ\Publish\API\Repository\Values\User\Limitation\RoleLimitation[]
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
