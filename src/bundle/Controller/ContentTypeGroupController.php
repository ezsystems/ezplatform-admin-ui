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
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;
use eZ\Publish\Core\MVC\Symfony\Security\Authorization\Attribute;

class ContentTypeGroupController extends Controller
{
    /** @var \EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface */
    private $notificationHandler;

    /** @var \Symfony\Component\Translation\TranslatorInterface */
    private $translator;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory */
    private $formFactory;

    /** @var \EzSystems\EzPlatformAdminUi\Form\SubmitHandler */
    private $submitHandler;

    /** @var array */
    private $languages;

    /** @var int */
    private $defaultPaginationLimit;

    /**
     * @param \EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface $notificationHandler
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     * @param \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory $formFactory
     * @param \EzSystems\EzPlatformAdminUi\Form\SubmitHandler $submitHandler
     * @param array $languages
     * @param int $defaultPaginationLimit
     */
    public function __construct(
        NotificationHandlerInterface $notificationHandler,
        TranslatorInterface $translator,
        ContentTypeService $contentTypeService,
        FormFactory $formFactory,
        SubmitHandler $submitHandler,
        array $languages,
        int $defaultPaginationLimit
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->translator = $translator;
        $this->contentTypeService = $contentTypeService;
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
        $this->languages = $languages;
        $this->defaultPaginationLimit = $defaultPaginationLimit;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request): Response
    {
        $deletableContentTypeGroup = [];
        $count = [];

        $page = $request->query->get('page') ?? 1;

        $pagerfanta = new Pagerfanta(
            new ArrayAdapter($this->contentTypeService->loadContentTypeGroups())
        );

        $pagerfanta->setMaxPerPage($this->defaultPaginationLimit);
        $pagerfanta->setCurrentPage(min($page, $pagerfanta->getNbPages()));

        /** @var \eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup[] $contentTypeGroupList */
        $contentTypeGroupList = $pagerfanta->getCurrentPageResults();

        $deleteContentTypeGroupsForm = $this->formFactory->deleteContentTypeGroups(
            new ContentTypeGroupsDeleteData($this->getContentTypeGroupsNumbers($contentTypeGroupList))
        );

        foreach ($contentTypeGroupList as $contentTypeGroup) {
            $contentTypesCount = count($this->contentTypeService->loadContentTypes($contentTypeGroup));
            $deletableContentTypeGroup[$contentTypeGroup->id] = !(bool)$contentTypesCount;
            $count[$contentTypeGroup->id] = $contentTypesCount;
        }

        return $this->render('@ezdesign/admin/content_type_group/list.html.twig', [
            'pager' => $pagerfanta,
            'form_content_type_groups_delete' => $deleteContentTypeGroupsForm->createView(),
            'deletable' => $deletableContentTypeGroup,
            'content_types_count' => $count,
            'can_create' => $this->isGranted(new Attribute('class', 'create')),
            'can_update' => $this->isGranted(new Attribute('class', 'update')),
            'can_delete' => $this->isGranted(new Attribute('class', 'delete')),
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request): Response
    {
        $this->denyAccessUnlessGranted(new Attribute('class', 'create'));
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

        return $this->render('@ezdesign/admin/content_type_group/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup $group
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request, ContentTypeGroup $group): Response
    {
        $this->denyAccessUnlessGranted(new Attribute('class', 'update'));
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

        return $this->render('@ezdesign/admin/content_type_group/edit.html.twig', [
            'content_type_group' => $group,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup $group
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Request $request, ContentTypeGroup $group): Response
    {
        $this->denyAccessUnlessGranted(new Attribute('class', 'delete'));
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
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function bulkDeleteAction(Request $request): Response
    {
        $this->denyAccessUnlessGranted(new Attribute('class', 'delete'));
        $form = $this->formFactory->deleteContentTypeGroups(
            new ContentTypeGroupsDeleteData()
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
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

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup $group
     * @param int $page
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction(Request $request, ContentTypeGroup $group, int $page = 1): Response
    {
        return $this->render('@ezdesign/admin/content_type_group/view.html.twig', [
            'content_type_group' => $group,
            'page' => $page,
            'route_name' => $request->get('_route'),
        ]);
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup[] $contentTypeGroups
     *
     * @return array
     */
    private function getContentTypeGroupsNumbers(array $contentTypeGroups): array
    {
        $contentTypeGroupsNumbers = array_column($contentTypeGroups, 'id');

        return array_combine($contentTypeGroupsNumbers, array_fill_keys($contentTypeGroupsNumbers, false));
    }
}
