<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup;
use EzSystems\EzPlatformAdminUi\Form\Data\ContentTypeGroup\ContentTypeGroupCreateData;
use EzSystems\EzPlatformAdminUi\Form\Data\ContentTypeGroup\ContentTypeGroupDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\ContentTypeGroup\ContentTypeGroupsDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\ContentTypeGroup\ContentTypeGroupUpdateData;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Form\SubmitHandler;
use EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use eZ\Publish\API\Repository\Exceptions\InvalidArgumentException;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use Symfony\Component\Translation\Exception\InvalidArgumentException as TranslationInvalidArgumentException;
use Symfony\Component\Translation\TranslatorInterface;

class ContentTypeGroupController extends Controller
{
    /** @var NotificationHandlerInterface */
    private $notificationHandler;

    /** @var TranslatorInterface */
    private $translator;

    /** @var ContentTypeService */
    private $contentTypeService;

    /** @var FormFactory */
    private $formFactory;

    /** @var SubmitHandler */
    private $submitHandler;

    /** @var array */
    private $languages;

    /**
     * ContentTypeGroupController constructor.
     *
     * @param NotificationHandlerInterface $notificationHandler
     * @param TranslatorInterface $translator
     * @param ContentTypeService $contentTypeService
     * @param FormFactory $formFactory
     * @param SubmitHandler $submitHandler
     * @param array $languages
     */
    public function __construct(
        NotificationHandlerInterface $notificationHandler,
        TranslatorInterface $translator,
        ContentTypeService $contentTypeService,
        FormFactory $formFactory,
        SubmitHandler $submitHandler,
        array $languages
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->translator = $translator;
        $this->contentTypeService = $contentTypeService;
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
        $this->languages = $languages;
    }

    public function listAction(): Response
    {
        $deletableContentTypeGroup = [];

        $contentTypeGroupList = $this->contentTypeService->loadContentTypeGroups();

        $deleteContentTypeGroupsForm = $this->formFactory->deleteContentTypeGroups(
            new ContentTypeGroupsDeleteData($this->getContentTypeGroupsNumbers($contentTypeGroupList))
        );

        foreach ($contentTypeGroupList as $contentTypeGroup) {
            $deletableContentTypeGroup[$contentTypeGroup->id] = !(bool)count($this->contentTypeService->loadContentTypes($contentTypeGroup));
        }

        return $this->render('@EzPlatformAdminUi/admin/content_type_group/list.html.twig', [
            'content_type_groups' => $contentTypeGroupList,
            'form_content_type_groups_delete' => $deleteContentTypeGroupsForm->createView(),
            'deletable' => $deletableContentTypeGroup,
        ]);
    }

    public function createAction(Request $request): Response
    {
        $form = $this->formFactory->createContentTypeGroup(
            new ContentTypeGroupCreateData()
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (ContentTypeGroupCreateData $data) {
                $createStruct = $this->contentTypeService->newContentTypeGroupCreateStruct(
                    $data->getIdentifier()
                );
                $group = $this->contentTypeService->createContentTypeGroup($createStruct);

                $this->notificationHandler->success(
                    $this->translator->trans(
                        /** @Desc("Content type group '%name%' created.") */
                        'content_type_group.create.success',
                        ['%name%' => $data->getIdentifier()],
                        'content_type'
                    )
                );

                return new RedirectResponse($this->generateUrl('ezplatform.content_type_group.view', [
                    'contentTypeGroupId' => $group->id,
                ]));
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->render('@EzPlatformAdminUi/admin/content_type_group/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function updateAction(Request $request, ContentTypeGroup $group): Response
    {
        $form = $this->formFactory->updateContentTypeGroup(
            new ContentTypeGroupUpdateData($group)
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (ContentTypeGroupUpdateData $data) {
                $group = $data->getContentTypeGroup();
                $updateStruct = $this->contentTypeService->newContentTypeGroupUpdateStruct();
                $updateStruct->identifier = $data->getIdentifier();

                $this->contentTypeService->updateContentTypeGroup($group, $updateStruct);

                $this->notificationHandler->success(
                    $this->translator->trans(
                        /** @Desc("Content type group '%name%' updated.") */
                        'content_type_group.update.success',
                        ['%name%' => $group->identifier],
                        'content_type'
                    )
                );

                return new RedirectResponse($this->generateUrl('ezplatform.content_type_group.view', [
                    'contentTypeGroupId' => $group->id,
                ]));
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->render('@EzPlatformAdminUi/admin/content_type_group/edit.html.twig', [
            'content_type_group' => $group,
            'form' => $form->createView(),
        ]);
    }

    public function deleteAction(Request $request, ContentTypeGroup $group): Response
    {
        $form = $this->formFactory->deleteContentTypeGroup(
            new ContentTypeGroupDeleteData($group)
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (ContentTypeGroupDeleteData $data) {
                $group = $data->getContentTypeGroup();
                $this->contentTypeService->deleteContentTypeGroup($group);

                $this->notificationHandler->success(
                    $this->translator->trans(
                        /** @Desc("Content type group '%name%' deleted.") */
                        'content_type_group.delete.success',
                        ['%name%' => $group->identifier],
                        'content_type'
                    )
                );
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirect($this->generateUrl('ezplatform.content_type_group.list'));
    }

    /**
     * Handles removing content type groups based on submitted form.
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws TranslationInvalidArgumentException
     * @throws InvalidOptionsException
     * @throws UnauthorizedException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws \InvalidArgumentException
     */
    public function bulkDeleteAction(Request $request): Response
    {
        $form = $this->formFactory->deleteContentTypeGroups(
            new ContentTypeGroupsDeleteData()
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $result = $this->submitHandler->handle($form, function (ContentTypeGroupsDeleteData $data) {
                foreach ($data->getContentTypeGroups() as $contentTypeGroupId => $selected) {
                    $group = $this->contentTypeService->loadContentTypeGroup($contentTypeGroupId);
                    $this->contentTypeService->deleteContentTypeGroup($group);

                    $this->notificationHandler->success(
                        $this->translator->trans(
                            /** @Desc("Content type group '%name%' deleted.") */
                            'content_type_group.delete.success',
                            ['%name%' => $group->identifier],
                            'content_type'
                        )
                    );
                }
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirect($this->generateUrl('ezplatform.content_type_group.list'));
    }

    public function viewAction(ContentTypeGroup $group): Response
    {
        return $this->render('@EzPlatformAdminUi/admin/content_type_group/view.html.twig', [
            'content_type_group' => $group,
        ]);
    }

    /**
     * @param ContentTypeGroup[] $contentTypeGroups
     *
     * @return array
     */
    private function getContentTypeGroupsNumbers(array $contentTypeGroups): array
    {
        $contentTypeGroupsNumbers = array_column($contentTypeGroups, 'id');

        return array_combine($contentTypeGroupsNumbers, array_fill_keys($contentTypeGroupsNumbers, false));
    }
}
