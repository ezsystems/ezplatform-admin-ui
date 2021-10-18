<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\ObjectStateService;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\ObjectState\ObjectState;
use eZ\Publish\API\Repository\Values\ObjectState\ObjectStateGroup;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\Core\MVC\Symfony\Security\Authorization\Attribute;
use EzSystems\EzPlatformAdminUi\Form\Data\ObjectState\ContentObjectStateUpdateData;
use EzSystems\EzPlatformAdminUi\Form\Data\ObjectState\ObjectStateCreateData;
use EzSystems\EzPlatformAdminUi\Form\Data\ObjectState\ObjectStateDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\ObjectState\ObjectStatesDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\ObjectState\ObjectStateUpdateData;
use EzSystems\EzPlatformAdminUi\Form\SubmitHandler;
use EzSystems\EzPlatformAdminUi\Form\Type\ObjectState\ContentObjectStateUpdateType;
use EzSystems\EzPlatformAdminUi\Form\Type\ObjectState\ObjectStateCreateType;
use EzSystems\EzPlatformAdminUi\Form\Type\ObjectState\ObjectStateDeleteType;
use EzSystems\EzPlatformAdminUi\Form\Type\ObjectState\ObjectStatesDeleteType;
use EzSystems\EzPlatformAdminUi\Form\Type\ObjectState\ObjectStateUpdateType;
use EzSystems\EzPlatformAdminUi\Notification\TranslatableNotificationHandlerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ObjectStateController extends Controller
{
    /** @var \EzSystems\EzPlatformAdminUi\Notification\TranslatableNotificationHandlerInterface */
    private $notificationHandler;

    /** @var \eZ\Publish\API\Repository\ObjectStateService */
    private $objectStateService;

    /** @var \Symfony\Component\Form\FormFactoryInterface */
    private $formFactory;

    /** @var \EzSystems\EzPlatformAdminUi\Form\SubmitHandler */
    private $submitHandler;

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    public function __construct(
        TranslatableNotificationHandlerInterface $notificationHandler,
        ObjectStateService $objectStateService,
        FormFactoryInterface $formFactory,
        SubmitHandler $submitHandler,
        PermissionResolver $permissionResolver,
        ConfigResolverInterface $configResolver
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->objectStateService = $objectStateService;
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
        $this->permissionResolver = $permissionResolver;
        $this->configResolver = $configResolver;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\ObjectState\ObjectStateGroup $objectStateGroup
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(ObjectStateGroup $objectStateGroup): Response
    {
        /** @var \eZ\Publish\API\Repository\Values\ObjectState\ObjectState[] $objectStates */
        $objectStates = $this->objectStateService->loadObjectStates($objectStateGroup);

        $deleteObjectStatesForm = $this->formFactory->create(
            ObjectStatesDeleteType::class,
            new ObjectStatesDeleteData($this->getObjectStatesIds($objectStates))
        );

        $unusedObjectStates = [];

        foreach ($objectStates as $state) {
            $unusedObjectStates[$state->id] = empty($this->objectStateService->getContentCount($state));
        }

        return $this->render('@ezdesign/object_state/list.html.twig', [
            'can_administrate' => $this->isGranted(new Attribute('state', 'administrate')),
            'object_state_group' => $objectStateGroup,
            'object_states' => $objectStates,
            'unused_object_states' => $unusedObjectStates,
            'form_states_delete' => $deleteObjectStatesForm->createView(),
        ]);
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\ObjectState\ObjectState $objectState
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction(ObjectState $objectState): Response
    {
        $deleteForm = $this->formFactory->create(
            ObjectStateDeleteType::class,
            new ObjectStateDeleteData($objectState)
        )->createView();

        return $this->render('@ezdesign/object_state/view.html.twig', [
            'can_administrate' => $this->isGranted(new Attribute('state', 'administrate')),
            'object_state_group' => $objectState->getObjectStateGroup(),
            'object_state' => $objectState,
            'delete_form' => $deleteForm,
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \eZ\Publish\API\Repository\Values\ObjectState\ObjectStateGroup $objectStateGroup
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $request, ObjectStateGroup $objectStateGroup): Response
    {
        $this->denyAccessUnlessGranted(new Attribute('state', 'administrate'));
        $languages = $this->configResolver->getParameter('languages');
        $defaultLanguageCode = reset($languages);

        $form = $this->formFactory->create(
            ObjectStateCreateType::class,
            new ObjectStateCreateData()
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form,
                function (ObjectStateCreateData $data) use ($defaultLanguageCode, $objectStateGroup) {
                    $createStruct = $this->objectStateService->newObjectStateCreateStruct(
                        $data->getIdentifier()
                    );
                    $createStruct->defaultLanguageCode = $defaultLanguageCode;
                    $createStruct->names = [$defaultLanguageCode => $data->getName()];
                    $objectState = $this->objectStateService->createObjectState($objectStateGroup, $createStruct);

                    $this->notificationHandler->success(
                            /** @Desc("Object state '%name%' created.") */
                            'object_state.create.success',
                            ['%name%' => $data->getName()],
                            'object_state'
                    );

                    return $this->redirectToRoute('ezplatform.object_state.state.view', [
                        'objectStateId' => $objectState->id,
                    ]);
                });
            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->render('@ezdesign/object_state/add.html.twig', [
            'object_state_group' => $objectStateGroup,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \eZ\Publish\API\Repository\Values\ObjectState\ObjectState $objectState
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Request $request, ObjectState $objectState): Response
    {
        $this->denyAccessUnlessGranted(new Attribute('state', 'administrate'));
        $form = $this->formFactory->create(
            ObjectStateDeleteType::class,
            new ObjectStateDeleteData($objectState)
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (ObjectStateDeleteData $data) {
                $objectState = $data->getObjectState();
                $this->objectStateService->deleteObjectState($objectState);

                $this->notificationHandler->success(
                    /** @Desc("Object state '%name%' deleted.") */
                    'object_state.delete.success',
                    ['%name%' => $objectState->getName()],
                    'object_state'
                );
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirectToRoute('ezplatform.object_state.group.view', [
            'objectStateGroupId' => $objectState->getObjectStateGroup()->id,
        ]);
    }

    /**
     * Handles removing object state groups based on submitted form.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $objectStateGroupId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function bulkDeleteAction(Request $request, int $objectStateGroupId): Response
    {
        $this->denyAccessUnlessGranted(new Attribute('state', 'administrate'));
        $form = $this->formFactory->create(
            ObjectStatesDeleteType::class,
            new ObjectStatesDeleteData()
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (ObjectStatesDeleteData $data) {
                foreach ($data->getObjectStates() as $objectStateId => $selected) {
                    $objectState = $this->objectStateService->loadObjectState($objectStateId);
                    $this->objectStateService->deleteObjectState($objectState);

                    $this->notificationHandler->success(
                        /** @Desc("Object state '%name%' deleted.") */
                        'object_state.delete.success',
                        ['%name%' => $objectState->getName()],
                        'object_state'
                    );
                }
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirectToRoute('ezplatform.object_state.group.view', [
            'objectStateGroupId' => $objectStateGroupId,
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \eZ\Publish\API\Repository\Values\ObjectState\ObjectState $objectState
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request, ObjectState $objectState): Response
    {
        $this->denyAccessUnlessGranted(new Attribute('state', 'administrate'));
        $form = $this->formFactory->create(
            ObjectStateUpdateType::class,
            new ObjectStateUpdateData($objectState)
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (ObjectStateUpdateData $data) {
                $objectState = $data->getObjectState();
                $updateStruct = $this->objectStateService->newObjectStateUpdateStruct();
                $updateStruct->identifier = $data->getIdentifier();
                $updateStruct->names[$objectState->mainLanguageCode] = $data->getName();

                $updatedObjectState = $this->objectStateService->updateObjectState($objectState, $updateStruct);

                $this->notificationHandler->success(
                    /** @Desc("Object state '%name%' updated.") */
                    'object_state.update.success',
                    ['%name%' => $updatedObjectState->getName()],
                    'object_state'
                );

                return $this->redirectToRoute('ezplatform.object_state.state.view', [
                    'objectStateId' => $objectState->id,
                ]);
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->render('@ezdesign/object_state/edit.html.twig', [
            'object_state_group' => $objectState->getObjectStateGroup(),
            'object_state' => $objectState,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \eZ\Publish\API\Repository\Values\Content\ContentInfo $contentInfo
     * @param \eZ\Publish\API\Repository\Values\ObjectState\ObjectStateGroup $objectStateGroup
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function updateContentStateAction(
        Request $request,
        ContentInfo $contentInfo,
        ObjectStateGroup $objectStateGroup
    ): Response {
        if (!$this->permissionResolver->hasAccess('state', 'assign')) {
            $exception = $this->createAccessDeniedException();
            $exception->setAttributes('state');
            $exception->setSubject('assign');

            throw $exception;
        }

        $form = $this->formFactory->create(
            ContentObjectStateUpdateType::class,
            new ContentObjectStateUpdateData($contentInfo, $objectStateGroup)
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (ContentObjectStateUpdateData $data) {
                $contentInfo = $data->getContentInfo();
                $objectStateGroup = $data->getObjectStateGroup();
                $objectState = $data->getObjectState();
                $this->objectStateService->setContentState($contentInfo, $objectStateGroup, $objectState);

                $this->notificationHandler->success(
                    /** @Desc("Content item's Object state changed to '%name%'.") */
                    'content_object_state.update.success',
                    ['%name%' => $objectState->getName()],
                    'object_state'
                );
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirectToRoute('_ez_content_view', [
            'contentId' => $contentInfo->id,
            'locationId' => $contentInfo->mainLocationId,
            '_fragment' => 'ibexa-tab-location-view-details',
        ]);
    }

    /**
     * @param array $states
     *
     * @return array
     */
    private function getObjectStatesIds(array $states): array
    {
        $statesIds = array_column($states, 'id');

        return array_combine($statesIds, array_fill_keys($statesIds, false));
    }
}
