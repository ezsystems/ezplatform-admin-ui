<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\RoleService;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\Values\User\Limitation\SectionLimitation;
use eZ\Publish\API\Repository\Values\User\Limitation\SubtreeLimitation;
use eZ\Publish\API\Repository\Values\User\Role;
use eZ\Publish\API\Repository\Values\User\RoleAssignment;
use EzSystems\EzPlatformAdminUi\Form\Data\Role\RoleAssignmentCreateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Role\RoleAssignmentDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Form\SubmitHandler;
use EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

class RoleAssignmentController extends Controller
{
    /** @var NotificationHandlerInterface */
    private $notificationHandler;

    /** @var TranslatorInterface */
    private $translator;

    /** @var RoleService */
    private $roleService;

    /** @var SearchService */
    private $searchService;

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
     * @param SearchService $searchService
     * @param FormFactory $formFactory
     * @param SubmitHandler $submitHandler
     */
    public function __construct(
        NotificationHandlerInterface $notificationHandler,
        TranslatorInterface $translator,
        RoleService $roleService,
        SearchService $searchService,
        FormFactory $formFactory,
        SubmitHandler $submitHandler
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->translator = $translator;
        $this->roleService = $roleService;
        $this->searchService = $searchService;
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
    }

    public function listAction(Role $role): Response
    {
        $roleViewUrl = $this->generateUrl('ezplatform.role.view', ['roleId' => $role->id]);

        $assignments = $this->roleService->getRoleAssignments($role);
        $deleteFormsByAssignmentId = [];

        foreach ($assignments as $assignment) {
            $deleteFormsByAssignmentId[$assignment->id] = $this->formFactory->createRoleAssignment(
                new RoleAssignmentDeleteData($assignment),
                $roleViewUrl,
                $roleViewUrl
            )->createView();
        }

        return $this->render('@EzPlatformAdminUi/admin/role_assignment/list.html.twig', [
            'role' => $role,
            'deleteFormsByAssignmentId' => $deleteFormsByAssignmentId,
            'assignments' => $assignments,
        ]);
    }

    public function createAction(Request $request, Role $role): Response
    {
        $roleViewUrl = $this->generateUrl('ezplatform.role.view', ['roleId' => $role->id]);

        $form = $this->formFactory->createRoleAssignment(
            new RoleAssignmentCreateData(),
            $roleViewUrl,
            $roleViewUrl
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (RoleAssignmentCreateData $data) use ($role) {
                $users = $data->getUsers();
                $groups = $data->getGroups();
                $sections = $data->getSections();
                $locations = $data->getLocations();

                $limitations = [];

                if (empty($sections) && empty($locations)) {
                    $limitations[] = null;
                } else {
                    if (!empty($sections)) {
                        $limitation = new SectionLimitation();

                        foreach ($sections as $section) {
                            $limitation->limitationValues[] = $section->id;
                        }

                        $limitations[] = $limitation;
                    }

                    if (!empty($locations)) {
                        $limitation = new SubtreeLimitation();

                        foreach ($locations as $location) {
                            $limitation->limitationValues[] = $location->pathString;
                        }

                        $limitations[] = $limitation;
                    }
                }

                foreach ($limitations as $limitation) {
                    if (!empty($users)) {
                        foreach ($users as $user) {
                            $this->roleService->assignRoleToUser($role, $user, $limitation);
                        }
                    }

                    if (!empty($groups)) {
                        foreach ($groups as $group) {
                            $this->roleService->assignRoleToUserGroup($role, $group, $limitation);
                        }
                    }
                }

                $this->notificationHandler->success(
                    $this->translator->trans(
                        /** @Desc("Assignments on role '%role%' created.") */ 'role.assignment_create.success',
                        ['%role%' => $role->identifier],
                        'role'
                    )
                );
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->render('@EzPlatformAdminUi/admin/role_assignment/add.html.twig', [
            'role' => $role,
            'form' => $form->createView(),
        ]);
    }

    public function deleteAction(Request $request, Role $role, RoleAssignment $roleAssignment): Response
    {
        $roleViewUrl = $this->generateUrl('ezplatform.role.view', ['roleId' => $role->id]);

        $form = $this->formFactory->deleteRoleAssignment(
            new RoleAssignmentDeleteData($roleAssignment),
            $roleViewUrl,
            $roleViewUrl
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (RoleAssignmentDeleteData $data) use ($role) {
                $roleAssignment = $data->getRoleAssignment();
                $this->roleService->removeRoleAssignment($roleAssignment);

                $this->notificationHandler->success(
                    $this->translator->trans(
                        /** @Desc("Assignment on role '%role%' removed.") */ 'role.assignment_delete.success',
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
