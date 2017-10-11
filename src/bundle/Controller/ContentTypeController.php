<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\API\Repository\Values\ContentType\ContentTypeDraft;
use eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use EzSystems\EzPlatformAdminUi\Service\CacheService;
use EzSystems\EzPlatformAdminUi\Service\ContentTypeService;
use EzSystems\RepositoryForms\Data\Mapper\ContentTypeDraftMapper;
use EzSystems\RepositoryForms\Form\ActionDispatcher\ActionDispatcherInterface;
use EzSystems\RepositoryForms\Form\Type\ContentType\ContentTypeUpdateType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ContentTypeController extends Controller
{
    /**
     * @var ContentTypeService
     */
    private $contentTypeService;

    /**
     * @var ActionDispatcherInterface
     */
    private $contentTypeActionDispatcher;

    /**
     * ContentTypeController constructor.
     *
     * @param ContentTypeService $contentTypeService
     * @param ActionDispatcherInterface $contentTypeActionDispatcher
     */
    public function __construct(
        ContentTypeService $contentTypeService,
        ActionDispatcherInterface $contentTypeActionDispatcher)
    {
        $this->contentTypeService = $contentTypeService;
        $this->contentTypeActionDispatcher = $contentTypeActionDispatcher;
    }

    public function listAction(ContentTypeGroup $group): Response
    {
        $types = $this->contentTypeService->getContentTypes($group);

        return $this->render('@EzPlatformAdminUi/admin/content_type/list.html.twig', [
            'content_type_group' => $group,
            'content_types' => $types,
        ]);
    }

    public function addAction(ContentTypeGroup $group): Response
    {
        $contentType = $this->contentTypeService->createContentType($group);

        return $this->redirectToRoute('ezplatform.content_type.edit', [
            'contentTypeGroupId' => $group->id,
            'contentTypeId' => $contentType->id,
        ]);
    }

    public function editAction(ContentTypeGroup $group, ContentTypeDraft $contentType): Response
    {
        $form = $this->createUpdateForm($group, $contentType);

        return $this->render('@EzPlatformAdminUi/admin/content_type/edit.html.twig', [
            'content_type_group' => $group,
            'content_type' => $contentType,
            'form' => $form->createView(),
        ]);
    }

    public function updateAction(CacheService $cacheService, Request $request, ContentTypeGroup $group, ContentTypeDraft $contentType): Response
    {
        $form = $this->createUpdateForm($group, $contentType);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $languageCode = $this->contentTypeService->getPrioritizedLanguage($contentType);

            // FIXME: Move to ContentTypeService
            $this->contentTypeActionDispatcher->dispatchFormAction(
                $form,
                $form->getData(),
                $form->getClickedButton() ? $form->getClickedButton()->getName() : null,
                ['languageCode' => $languageCode]
            );

            $cacheService->clearContentTypesCache();

            if ($response = $this->contentTypeActionDispatcher->getResponse()) {
                return $response;
            }

            $this->flashSuccess('content_type.updated', [], 'content_type');

            $routeName = 'publishContentType' === $form->getClickedButton()->getName()
                ? 'ezplatform.content_type.view'
                : 'ezplatform.content_type.edit';

            return $this->redirectToRoute($routeName, [
                'contentTypeGroupId' => $group->id,
                'contentTypeId' => $contentType->id,
            ]);
        }

        return $this->render('@EzPlatformAdminUi/admin/content_type/edit.html.twig', [
            'content_type_group' => $group,
            'content_type' => $contentType,
            'form' => $form->createView(),
        ]);
    }

    public function deleteAction(Request $request, ContentTypeGroup $group, ContentType $contentType): Response
    {
        $form = $this->createDeleteForm($group, $contentType);
        $form->handleRequest($request);
        if ($form->isValid()) {
            try {
                $this->contentTypeService->deleteContentType($contentType);
                $this->flashSuccess('content_type.deleted', [], 'content_type');
            } catch (InvalidArgumentException $e) {
                $this->flashDanger($e->getMessage());
            }
        }

        return $this->redirectToRoute('ezplatform.content_type_group.view', [
            'contentTypeGroupId' => $group->id,
        ]);
    }

    public function viewAction(ContentTypeGroup $group, ContentType $contentType): Response
    {
        $fieldDefinitionsByGroup = [];
        foreach ($contentType->fieldDefinitions as $fieldDefinition) {
            $fieldDefinitionsByGroup[$fieldDefinition->fieldGroup ?: 'content'][] = $fieldDefinition;
        }

        return $this->render('@EzPlatformAdminUi/admin/content_type/view.html.twig', [
            'content_type_group' => $group,
            'content_type' => $contentType,
            'field_definitions_by_group' => $fieldDefinitionsByGroup,
        ]);
    }

    public function createUpdateForm(ContentTypeGroup $group, ContentTypeDraft $contentTypeDraft): Form
    {
        $contentTypeData = (new ContentTypeDraftMapper())->mapToFormData(
            $contentTypeDraft
        );

        $languageCode = $this->contentTypeService->getPrioritizedLanguage($contentTypeDraft);

        return $this->createForm(ContentTypeUpdateType::class, $contentTypeData, [
            'method' => Request::METHOD_POST,
            'action' => $this->generateUrl('ezplatform.content_type.update', [
                'contentTypeGroupId' => $group->id,
                'contentTypeId' => $contentTypeDraft->id,
            ]),
            'languageCode' => $languageCode,
        ]);
    }

    protected function createDeleteForm(ContentTypeGroup $group, ContentType $contentType): Form
    {
        $formBuilder = $this->createFormBuilder(null, [
            'method' => Request::METHOD_DELETE,
            'action' => $this->generateUrl('ezplatform.content_type.delete', [
                'contentTypeGroupId' => $group->id,
                'contentTypeId' => $contentType->id,
            ]),
        ]);

        return $formBuilder->getForm();
    }
}
