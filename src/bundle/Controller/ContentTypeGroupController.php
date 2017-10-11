<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use EzSystems\EzPlatformAdminUi\Form\Data\ContentTypeGroupData;
use EzSystems\EzPlatformAdminUi\Form\Type\ContentTypeGroup\ContentTypeGroupType;
use EzSystems\EzPlatformAdminUi\Service\ContentTypeGroup\ContentTypeGroupService;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ContentTypeGroupController extends Controller
{
    /** @var ContentTypeGroupService */
    private $contentTypeGroupService;

    /**
     * ContentTypeGroupController constructor.
     *
     * @param ContentTypeGroupService $groupService
     */
    public function __construct(ContentTypeGroupService $groupService)
    {
        $this->contentTypeGroupService = $groupService;
    }

    public function listAction(): Response
    {
        $groups = $this->contentTypeGroupService->getContentTypeGroups();

        return $this->render('@EzPlatformAdminUi/admin/content_type_group/list.html.twig', [
            'content_type_groups' => $groups,
        ]);
    }

    public function addAction(): Response
    {
        $form = $this->createCreateForm();

        return $this->render('@EzPlatformAdminUi/admin/content_type_group/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function createAction(Request $request): Response
    {
        $form = $this->createCreateForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $group = $this->contentTypeGroupService->createContentTypeGroup($form->getData());
            $this->flashSuccess('content_type_group.created', [], 'content_type');

            return $this->redirectToRoute('ezplatform.content_type_group.view', [
                'contentTypeGroupId' => $group->id,
            ]);
        }

        return $this->render('@EzPlatformAdminUi/admin/content_type_group/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function editAction(ContentTypeGroup $group): Response
    {
        $form = $this->createUpdateForm($group);

        return $this->render('@EzPlatformAdminUi/admin/content_type_group/edit.html.twig', [
            'content_type_group' => $group,
            'form' => $form->createView(),
        ]);
    }

    public function updateAction(Request $request, ContentTypeGroup $group): Response
    {
        $form = $this->createUpdateForm($group);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->contentTypeGroupService->updateContentTypeGroup($group, $form->getData());
            $this->flashSuccess('content_type_group.updated', [], 'content_type');

            return $this->redirectToRoute('ezplatform.content_type_group.view', [
                'contentTypeGroupId' => $group->id,
            ]);
        }

        return $this->render('@EzPlatformAdminUi/admin/content_type_group/edit.html.twig', [
            'content_type_group' => $group,
            'form' => $form->createView(),
        ]);
    }

    public function deleteAction(Request $request, ContentTypeGroup $group): Response
    {
        $form = $this->createDeleteForm($group);
        $form->handleRequest($request);
        if ($form->isValid()) {
            try {
                $this->contentTypeGroupService->deleteContentTypeGroup($group);
                $this->flashSuccess('content_type_group.deleted', [], 'content_type');
            } catch (InvalidArgumentException $e) {
                $this->flashDanger($e->getMessage());
            }
        }

        return $this->redirectToRoute('ezplatform.content_type_group.list');
    }

    public function viewAction(ContentTypeGroup $group): Response
    {
        return $this->render('@EzPlatformAdminUi/admin/content_type_group/view.html.twig', [
            'content_type_group' => $group,
        ]);
    }

    protected function createCreateForm(ContentTypeGroupData $data = null): Form
    {
        return $this->createForm(ContentTypeGroupType::class, $data, [
            'method' => Request::METHOD_POST,
            'action' => $this->generateUrl('ezplatform.content_type_group.create'),
        ]);
    }

    protected function createUpdateForm(ContentTypeGroup $group, ContentTypeGroupData $data = null): Form
    {
        if (null === $data) {
            $data = ContentTypeGroupData::factory($group);
        }

        return $this->createForm(ContentTypeGroupType::class, $data, [
            'method' => Request::METHOD_PUT,
            'action' => $this->generateUrl('ezplatform.content_type_group.update', [
                'contentTypeGroupId' => $group->id,
            ]),
        ]);
    }

    protected function createDeleteForm(ContentTypeGroup $group): Form
    {
        $formBuilder = $this->createFormBuilder(null, [
            'method' => Request::METHOD_DELETE,
            'action' => $this->generateUrl('ezplatform.content_type_group.delete', [
                'contentTypeGroupId' => $group->id,
            ]),
        ]);

        return $formBuilder->getForm();
    }
}
