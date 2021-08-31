<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\ObjectStateService;
use eZ\Publish\API\Repository\Values\ObjectState\ObjectStateGroup;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\Core\MVC\Symfony\Security\Authorization\Attribute;
use EzSystems\EzPlatformAdminUi\Form\Data\ObjectState\ObjectStateGroupCreateData;
use EzSystems\EzPlatformAdminUi\Form\Data\ObjectState\ObjectStateGroupDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\ObjectState\ObjectStateGroupsDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\ObjectState\ObjectStateGroupUpdateData;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Form\SubmitHandler;
use EzSystems\EzPlatformAdminUi\Notification\TranslatableNotificationHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ObjectStateGroupController extends Controller
{
    /** @var \EzSystems\EzPlatformAdminUi\Notification\TranslatableNotificationHandlerInterface */
    private $notificationHandler;

    /** @var \eZ\Publish\API\Repository\ObjectStateService */
    private $objectStateService;

    /** @var \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory */
    private $formFactory;

    /** @var \EzSystems\EzPlatformAdminUi\Form\SubmitHandler */
    private $submitHandler;

    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    public function __construct(
        TranslatableNotificationHandlerInterface $notificationHandler,
        ObjectStateService $objectStateService,
        FormFactory $formFactory,
        SubmitHandler $submitHandler,
        ConfigResolverInterface $configResolver
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->objectStateService = $objectStateService;
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
        $this->configResolver = $configResolver;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(): Response
    {
        /** @var \eZ\Publish\API\Repository\Values\ObjectState\ObjectStateGroup[] $objectStateGroups */
        $objectStateGroups = $this->objectStateService->loadObjectStateGroups();
        $emptyObjectStateGroups = [];

        foreach ($objectStateGroups as $group) {
            $emptyObjectStateGroups[$group->id] = empty($this->objectStateService->loadObjectStates($group));
        }

        $deleteObjectStateGroupsForm = $this->formFactory->deleteObjectStateGroups(
            new ObjectStateGroupsDeleteData($this->getObjectStateGroupsIds($objectStateGroups))
        );

        return $this->render('@ezdesign/object_state/object_state_group/list.html.twig', [
            'can_administrate' => $this->isGranted(new Attribute('state', 'administrate')),
            'object_state_groups' => $objectStateGroups,
            'empty_object_state_groups' => $emptyObjectStateGroups,
            'form_state_groups_delete' => $deleteObjectStateGroupsForm->createView(),
        ]);
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\ObjectState\ObjectStateGroup $objectStateGroup
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction(ObjectStateGroup $objectStateGroup): Response
    {
        $deleteForm = $this->formFactory->deleteObjectStateGroup(
            new ObjectStateGroupDeleteData($objectStateGroup)
        )->createView();

        return $this->render('@ezdesign/object_state/object_state_group/view.html.twig', [
            'can_administrate' => $this->isGranted(new Attribute('state', 'administrate')),
            'object_state_group' => $objectStateGroup,
            'delete_form' => $deleteForm,
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $request): Response
    {
        $this->denyAccessUnlessGranted(new Attribute('state', 'administrate'));
        $languages = $this->configResolver->getParameter('languages');
        $defaultLanguageCode = reset($languages);

        $form = $this->formFactory->createObjectStateGroup(
            new ObjectStateGroupCreateData()
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form,
                function (ObjectStateGroupCreateData $data) use ($defaultLanguageCode) {
                    $createStruct = $this->objectStateService->newObjectStateGroupCreateStruct(
                        $data->getIdentifier()
                    );
                    $createStruct->defaultLanguageCode = $defaultLanguageCode;
                    $createStruct->names = [$defaultLanguageCode => $data->getName()];
                    $group = $this->objectStateService->createObjectStateGroup($createStruct);

                    $this->notificationHandler->success(
                        /** @Desc("Object state group '%name%' created.") */
                        'object_state_group.create.success',
                        ['%name%' => $data->getName()],
                        'object_state'
                    );

                    return $this->redirectToRoute('ezplatform.object_state.group.view', [
                        'objectStateGroupId' => $group->id,
                    ]);
                });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->render('@ezdesign/object_state/object_state_group/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \eZ\Publish\API\Repository\Values\ObjectState\ObjectStateGroup $group
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Request $request, ObjectStateGroup $group): Response
    {
        $this->denyAccessUnlessGranted(new Attribute('state', 'administrate'));
        $form = $this->formFactory->deleteObjectStateGroup(
            new ObjectStateGroupDeleteData($group)
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (ObjectStateGroupDeleteData $data) {
                $group = $data->getObjectStateGroup();
                $this->objectStateService->deleteObjectStateGroup($group);

                $this->notificationHandler->success(
                    /** @Desc("Object state group '%name%' deleted.") */
                    'object_state_group.delete.success',
                    ['%name%' => $group->getName()],
                    'object_state'
                );
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirectToRoute('ezplatform.object_state.groups.list');
    }

    /**
     * Handles removing object state groups based on submitted form.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function bulkDeleteAction(Request $request): Response
    {
        $this->denyAccessUnlessGranted(new Attribute('state', 'administrate'));
        $form = $this->formFactory->deleteObjectStateGroups(
            new ObjectStateGroupsDeleteData()
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (ObjectStateGroupsDeleteData $data) {
                foreach ($data->getObjectStateGroups() as $objectStateGroupId => $selected) {
                    $objectStateGroup = $this->objectStateService->loadObjectStateGroup($objectStateGroupId);
                    $this->objectStateService->deleteObjectStateGroup($objectStateGroup);

                    $this->notificationHandler->success(
                        /** @Desc("Object state group '%name%' deleted.") */
                        'object_state_group.delete.success',
                        ['%name%' => $objectStateGroup->getName()],
                        'object_state'
                    );
                }
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirectToRoute('ezplatform.object_state.groups.list');
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \eZ\Publish\API\Repository\Values\ObjectState\ObjectStateGroup $group
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request, ObjectStateGroup $group): Response
    {
        $this->denyAccessUnlessGranted(new Attribute('state', 'administrate'));
        $form = $this->formFactory->updateObjectStateGroup(
            new ObjectStateGroupUpdateData($group)
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (ObjectStateGroupUpdateData $data) {
                $group = $data->getObjectStateGroup();
                $updateStruct = $this->objectStateService->newObjectStateGroupUpdateStruct();
                $updateStruct->identifier = $data->getIdentifier();
                $updateStruct->names[$group->mainLanguageCode] = $data->getName();

                $updatedGroup = $this->objectStateService->updateObjectStateGroup($group, $updateStruct);

                $this->notificationHandler->success(
                    /** @Desc("Object state group '%name%' updated.") */
                    'object_state_group.update.success',
                    ['%name%' => $updatedGroup->getName()],
                    'object_state'
                );

                return $this->redirectToRoute('ezplatform.object_state.group.view', [
                    'objectStateGroupId' => $group->id,
                ]);
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->render('@ezdesign/object_state/object_state_group/edit.html.twig', [
            'object_state_group' => $group,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\ObjectState\ObjectStateGroup[] $groups
     *
     * @return array
     */
    private function getObjectStateGroupsIds(array $groups): array
    {
        $groupsIds = array_column($groups, 'id');

        return array_combine($groupsIds, array_fill_keys($groupsIds, false));
    }
}
