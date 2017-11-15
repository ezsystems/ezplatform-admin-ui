<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\ObjectStateService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\ObjectState\ObjectState;
use eZ\Publish\API\Repository\Values\ObjectState\ObjectStateGroup;
use eZ\Publish\Core\MVC\Symfony\Security\Authorization\Attribute;
use EzSystems\EzPlatformAdminUi\Form\Data\ObjectState\ContentObjectStateUpdateData;
use EzSystems\EzPlatformAdminUi\Form\Data\ObjectState\ObjectStateCreateData;
use EzSystems\EzPlatformAdminUi\Form\Data\ObjectState\ObjectStateDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\ObjectState\ObjectStateUpdateData;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Form\SubmitHandler;
use EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

class ObjectStateController extends Controller
{
    /** @var NotificationHandlerInterface */
    private $notificationHandler;

    /** @var TranslatorInterface */
    private $translator;

    /** @var ObjectStateService */
    private $objectStateService;

    /** @var FormFactory */
    private $formFactory;

    /** @var SubmitHandler */
    private $submitHandler;

    /** @var array */
    private $languages;

    /**
     * @param NotificationHandlerInterface $notificationHandler
     * @param TranslatorInterface $translator
     * @param ObjectStateService $objectStateService
     * @param FormFactory $formFactory
     * @param SubmitHandler $submitHandler
     * @param array $languages
     */
    public function __construct(
        NotificationHandlerInterface $notificationHandler,
        TranslatorInterface $translator,
        ObjectStateService $objectStateService,
        FormFactory $formFactory,
        SubmitHandler $submitHandler,
        array $languages
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->translator = $translator;
        $this->objectStateService = $objectStateService;
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
        $this->languages = $languages;
    }

    /**
     * @param ObjectStateGroup $objectStateGroup
     *
     * @return Response
     */
    public function listAction(ObjectStateGroup $objectStateGroup): Response
    {
        /** @var ObjectState[] $objectStates */
        $objectStates = $this->objectStateService->loadObjectStates($objectStateGroup);
        $deleteFormsByObjectStateId = [];

        foreach ($objectStates as $objectState) {
            $deleteFormsByObjectStateId[$objectState->id] = $this->formFactory->deleteObjectState(
                new ObjectStateDeleteData($objectState)
            )->createView();
        }

        return $this->render('EzPlatformAdminUiBundle:admin/object_state:list.html.twig', [
            'can_administrate' => $this->isGranted(new Attribute('state', 'administrate')),
            'object_state_group' => $objectStateGroup,
            'object_states' => $objectStates,
            'form_object_state_delete' => $deleteFormsByObjectStateId,
        ]);
    }

    /**
     * @param ObjectState $objectState
     *
     * @return Response
     */
    public function viewAction(ObjectState $objectState): Response
    {
        $delete_form = $this->formFactory->deleteObjectState(
            new ObjectStateDeleteData($objectState)
        )->createView();

        return $this->render('EzPlatformAdminUiBundle:admin/object_state:view.html.twig', [
            'object_state_group' => $objectState->getObjectStateGroup(),
            'object_state' => $objectState,
            'delete_form' => $delete_form,
        ]);
    }

    public function addAction(Request $request, ObjectStateGroup $objectStateGroup): Response
    {
        $defaultLanguageCode = reset($this->languages);

        $form = $this->formFactory->createObjectState(
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
                        $this->translator->trans(
                            /** @Desc("Object state '%name%' created.") */
                            'object_state.create.success',
                            ['%name%' => $data->getName()],
                            'object_state'
                        )
                    );

                    return new RedirectResponse($this->generateUrl('ezplatform.object_state.state.view', [
                        'objectStateId' => $objectState->id,
                    ]));
                });
            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->render('EzPlatformAdminUiBundle:admin/object_state:add.html.twig', [
            'object_state_group' => $objectStateGroup,
            'form' => $form->createView(),
        ]);
    }

    public function deleteAction(Request $request, ObjectState $objectState): Response
    {
        $form = $this->formFactory->deleteObjectState(
            new ObjectStateDeleteData($objectState)
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (ObjectStateDeleteData $data) {
                $objectState = $data->getObjectState();
                $this->objectStateService->deleteObjectState($objectState);

                $this->notificationHandler->success(
                    $this->translator->trans(
                        /** @Desc("Object state '%name%' deleted.") */
                        'object_state.delete.success',
                        ['%name%' => $objectState->identifier],
                        'object_state'
                    )
                );
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirect($this->generateUrl('ezplatform.object_state.group.view', [
            'objectStateGroupId' => $objectState->getObjectStateGroup()->id,
        ]));
    }

    public function updateAction(Request $request, ObjectState $objectState): Response
    {
        $form = $this->formFactory->updateObjectState(
            new ObjectStateUpdateData($objectState)
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (ObjectStateUpdateData $data) {
                $objectState = $data->getObjectState();
                $updateStruct = $this->objectStateService->newObjectStateUpdateStruct();
                $updateStruct->identifier = $data->getIdentifier();

                $this->objectStateService->updateObjectState($objectState, $updateStruct);

                $this->notificationHandler->success(
                    $this->translator->trans(
                        /** @Desc("Object state '%name%' updated.") */
                        'object_state.update.success',
                        ['%name%' => $objectState->identifier],
                        'object_state'
                    )
                );

                return new RedirectResponse($this->generateUrl('ezplatform.object_state.state.view', [
                    'objectStateId' => $objectState->id,
                ]));
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->render('@EzPlatformAdminUi/admin/object_state/edit.html.twig', [
            'object_state_group' => $objectState->getObjectStateGroup(),
            'object_state' => $objectState,
            'form' => $form->createView(),
        ]);
    }

    public function updateContentStateAction(
        Request $request,
        ContentInfo $contentInfo,
        ObjectStateGroup $objectStateGroup
    ): Response {
        $form = $this->formFactory->updateContentObjectState(
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
                    $this->translator->trans(
                        /** @Desc("Content object state '%name%' updated.") */
                        'content_object_state.update.success',
                        ['%name%' => $objectState->identifier],
                        'object_state'
                    )
                );
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirect($this->generateUrl('_ezpublishLocation', [
            'locationId' => $contentInfo->mainLocationId,
            '_fragment' => 'ez-tab-location-view-details',
        ]));
    }
}
