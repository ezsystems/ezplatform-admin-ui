<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\Core\MVC\Symfony\Security\Authorization\Attribute;
use EzSystems\EzPlatformAdminUi\Form\Data\ContentTypeGroup\ContentTypeGroupCreateData;
use EzSystems\EzPlatformAdminUi\Form\Data\ContentTypeGroup\ContentTypeGroupDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\ContentTypeGroup\ContentTypeGroupsDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\ContentTypeGroup\ContentTypeGroupUpdateData;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Form\SubmitHandler;
use EzSystems\EzPlatformAdminUi\Notification\TranslatableNotificationHandlerInterface;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ContentTypeGroupController extends Controller
{
    /** @var \EzSystems\EzPlatformAdminUi\Notification\TranslatableNotificationHandlerInterface */
    private $notificationHandler;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory */
    private $formFactory;

    /** @var \EzSystems\EzPlatformAdminUi\Form\SubmitHandler */
    private $submitHandler;

    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    public function __construct(
        TranslatableNotificationHandlerInterface $notificationHandler,
        ContentTypeService $contentTypeService,
        FormFactory $formFactory,
        SubmitHandler $submitHandler,
        ConfigResolverInterface $configResolver
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->contentTypeService = $contentTypeService;
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
        $this->configResolver = $configResolver;
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

        $pagerfanta->setMaxPerPage($this->configResolver->getParameter('pagination.content_type_group_limit'));
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

        return $this->render('@ezdesign/content_type/content_type_group/list.html.twig', [
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
                    /** @Desc("Created Content Type group '%name%'.") */
                    'content_type_group.create.success',
                    ['%name%' => $data->getIdentifier()],
                    'content_type'
                );

                return new RedirectResponse($this->generateUrl('ezplatform.content_type_group.view', [
                    'contentTypeGroupId' => $group->id,
                ]));
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->render('@ezdesign/content_type/content_type_group/create.html.twig', [
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
                    /** @Desc("Updated Content Type group '%name%'.") */
                    'content_type_group.update.success',
                    ['%name%' => $group->identifier],
                    'content_type'
                );

                return new RedirectResponse($this->generateUrl('ezplatform.content_type_group.view', [
                    'contentTypeGroupId' => $group->id,
                ]));
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->render('@ezdesign/content_type/content_type_group/edit.html.twig', [
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
                    /** @Desc("Deleted Content Type group '%name%'.") */
                    'content_type_group.delete.success',
                    ['%name%' => $group->identifier],
                    'content_type'
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
                        /** @Desc("Deleted Content Type group '%name%'.") */
                        'content_type_group.delete.success',
                        ['%name%' => $group->identifier],
                        'content_type'
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
        return $this->render('@ezdesign/content_type/content_type_group/index.html.twig', [
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
