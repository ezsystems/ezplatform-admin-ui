<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\ObjectStateService;
use eZ\Publish\API\Repository\Values\ObjectState\ObjectStateGroup;
use eZ\Publish\Core\MVC\Symfony\Security\Authorization\Attribute;
use EzSystems\EzPlatformAdminUi\Form\Data\ObjectState\ObjectStateGroupCreateData;
use EzSystems\EzPlatformAdminUi\Form\Data\ObjectState\ObjectStateGroupDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\ObjectState\ObjectStateGroupUpdateData;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Form\SubmitHandler;
use EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

class ObjectStateGroupController extends Controller
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
     * @return Response
     */
    public function listAction(): Response
    {
        /** @var ObjectStateGroup[] $objectStateGroups */
        $objectStateGroups = $this->objectStateService->loadObjectStateGroups();
        $deleteFormsByObjectStateGroupId = [];

        foreach ($objectStateGroups as $objectStateGroup) {
            $deleteFormsByObjectStateGroupId[$objectStateGroup->id] = $this->formFactory->deleteObjectStateGroup(
                new ObjectStateGroupDeleteData($objectStateGroup)
            )->createView();
        }

        return $this->render('EzPlatformAdminUiBundle:admin/object_state_group:list.html.twig', [
            'can_administrate' => $this->isGranted(new Attribute('state', 'administrate')),
            'object_state_groups' => $objectStateGroups,
            'form_object_state_group_delete' => $deleteFormsByObjectStateGroupId,
        ]);
    }

    /**
     * @param ObjectStateGroup $objectStateGroup
     *
     * @return Response
     */
    public function viewAction(ObjectStateGroup $objectStateGroup): Response
    {
        $delete_form = $this->formFactory->deleteObjectStateGroup(
            new ObjectStateGroupDeleteData($objectStateGroup)
        )->createView();

        return $this->render('EzPlatformAdminUiBundle:admin/object_state_group:view.html.twig', [
            'object_state_group' => $objectStateGroup,
            'delete_form' => $delete_form,
        ]);
    }

    public function addAction(Request $request): Response
    {
        $defaultLanguageCode = reset($this->languages);

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
                        $this->translator->trans(
                            /** @Desc("Object state type group '%name%' created.") */
                            'object_state_group.create.success',
                            ['%name%' => $data->getName()],
                            'object_state'
                        )
                    );

                    return new RedirectResponse($this->generateUrl('ezplatform.object_state.group.view', [
                        'objectStateGroupId' => $group->id,
                    ]));
                });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->render('EzPlatformAdminUiBundle:admin/object_state_group:add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function deleteAction(Request $request, ObjectStateGroup $group): Response
    {
        $form = $this->formFactory->deleteObjectStateGroup(
            new ObjectStateGroupDeleteData($group)
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (ObjectStateGroupDeleteData $data) {
                $group = $data->getObjectStateGroup();
                $this->objectStateService->deleteObjectStateGroup($group);

                $this->notificationHandler->success(
                    $this->translator->trans(
                        /** @Desc("Object state type group '%name%' deleted.") */
                        'object_state_group.delete.success',
                        ['%name%' => $group->identifier],
                        'object_state'
                    )
                );
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirect($this->generateUrl('ezplatform.object_state.groups.list'));
    }

    public function updateAction(Request $request, ObjectStateGroup $group): Response
    {
        $form = $this->formFactory->updateObjectStateGroup(
            new ObjectStateGroupUpdateData($group)
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (ObjectStateGroupUpdateData $data) {
                $group = $data->getObjectStateGroup();
                $updateStruct = $this->objectStateService->newObjectStateGroupUpdateStruct();
                $updateStruct->identifier = $data->getIdentifier();

                $this->objectStateService->updateObjectStateGroup($group, $updateStruct);

                $this->notificationHandler->success(
                    $this->translator->trans(
                        /** @Desc("Object state group '%name%' updated.") */
                        'object_state_group.update.success',
                        ['%name%' => $group->identifier],
                        'object_state'
                    )
                );

                return new RedirectResponse($this->generateUrl('ezplatform.object_state.group.view', [
                    'objectStateGroupId' => $group->id,
                ]));
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->render('@EzPlatformAdminUi/admin/object_state_group/edit.html.twig', [
            'object_state_group' => $group,
            'form' => $form->createView(),
        ]);
    }
}
