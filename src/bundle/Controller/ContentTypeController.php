<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\API\Repository\Values\ContentType\ContentTypeDraft;
use eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup;
use EzSystems\EzPlatformAdminUi\Form\Data\ContentType\ContentTypesDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Form\SubmitHandler;
use EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface;
use EzSystems\RepositoryForms\Data\Mapper\ContentTypeDraftMapper;
use EzSystems\RepositoryForms\Form\ActionDispatcher\ActionDispatcherInterface;
use EzSystems\RepositoryForms\Form\Type\ContentType\ContentTypeUpdateType;
use Symfony\Component\Form\Form;
use eZ\Publish\API\Repository\Exceptions\BadStateException;
use eZ\Publish\API\Repository\Exceptions\InvalidArgumentException;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\Translation\Exception\InvalidArgumentException as TranslationInvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

class ContentTypeController extends Controller
{
    /** @var NotificationHandlerInterface */
    private $notificationHandler;

    /** @var TranslatorInterface */
    private $translator;

    /** @var ContentTypeService */
    private $contentTypeService;

    /** @var ActionDispatcherInterface */
    private $contentTypeActionDispatcher;

    /** @var array */
    private $languages;

    /** @var FormFactory */
    private $formFactory;

    /** @var SubmitHandler */
    private $submitHandler;

    /**
     * ContentTypeController constructor.
     *
     * @param NotificationHandlerInterface $notificationHandler
     * @param TranslatorInterface $translator
     * @param ContentTypeService $contentTypeService
     * @param ActionDispatcherInterface $contentTypeActionDispatcher
     * @param FormFactory $formFactory
     * @param SubmitHandler $submitHandler
     * @param array $languages
     */
    public function __construct(
        NotificationHandlerInterface $notificationHandler,
        TranslatorInterface $translator,
        ContentTypeService $contentTypeService,
        ActionDispatcherInterface $contentTypeActionDispatcher,
        FormFactory $formFactory,
        SubmitHandler $submitHandler,
        array $languages
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->translator = $translator;
        $this->contentTypeService = $contentTypeService;
        $this->contentTypeActionDispatcher = $contentTypeActionDispatcher;
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
        $this->languages = $languages;
    }

    public function listAction(ContentTypeGroup $group): Response
    {
        $deletableTypes = [];

        $types = $this->contentTypeService->loadContentTypes($group, $this->languages);

        $deleteContentTypesForm = $this->formFactory->deleteContentTypes(
            new ContentTypesDeleteData($this->getContentTypesNumbers($types))
        );

        foreach ($types as $type) {
            $deletableTypes[$type->id] = !$this->contentTypeService->isContentTypeUsed($type);
        }

        return $this->render('@EzPlatformAdminUi/admin/content_type/list.html.twig', [
            'content_type_group' => $group,
            'content_types' => $types,
            'deletable' => $deletableTypes,
            'form_content_types_delete' => $deleteContentTypesForm->createView(),
            'group' => $group,
        ]);
    }

    public function addAction(ContentTypeGroup $group): Response
    {
        $mainLanguageCode = reset($this->languages);

        $createStruct = $this->contentTypeService->newContentTypeCreateStruct('__new__' . md5((string)microtime(true)));
        $createStruct->mainLanguageCode = $mainLanguageCode;
        $createStruct->names = [$mainLanguageCode => 'New Content Type'];

        $contentTypeDraft = $this->contentTypeService->createContentType($createStruct, [$group]);

        $form = $this->createUpdateForm($group, $contentTypeDraft);

        return $this->render('@EzPlatformAdminUi/admin/content_type/create.html.twig', [
            'content_type_group' => $group,
            'content_type' => $contentTypeDraft,
            'form' => $form->createView(),
        ]);
    }

    public function editAction(ContentTypeGroup $group, ContentType $contentType): Response
    {
        try {
            $contentTypeDraft = $this->contentTypeService->loadContentTypeDraft($contentType->id);
            $this->contentTypeService->deleteContentType($contentTypeDraft);
            $contentTypeDraft = $this->contentTypeService->createContentTypeDraft($contentType);
        } catch (NotFoundException $e) {
            $contentTypeDraft = $this->contentTypeService->createContentTypeDraft($contentType);
        }

        return $this->redirectToRoute('ezplatform.content_type.update', [
            'contentTypeId' => $contentTypeDraft->id,
            'contentTypeGroupId' => $group->id,
        ]);
    }

    public function updateAction(Request $request, ContentTypeGroup $group, ContentTypeDraft $contentTypeDraft): Response
    {
        $form = $this->createUpdateForm($group, $contentTypeDraft);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function () use ($form, $group, $contentTypeDraft) {
                $languageCode = reset($this->languages);

                foreach ($this->languages as $prioritizedLanguage) {
                    if (isset($contentTypeDraft->names[$prioritizedLanguage])) {
                        $languageCode = $prioritizedLanguage;
                        break;
                    }
                }

                $this->contentTypeActionDispatcher->dispatchFormAction(
                    $form,
                    $form->getData(),
                    $form->getClickedButton() ? $form->getClickedButton()->getName() : null,
                    ['languageCode' => $languageCode]
                );

                if ($response = $this->contentTypeActionDispatcher->getResponse()) {
                    return $response;
                }

                $this->notificationHandler->success(
                    $this->translator->trans(
                        /** @Desc("Content type '%name%' updated.") */
                        'content_type.update.success',
                        ['%name%' => $contentTypeDraft->getName()],
                        'content_type'
                    )
                );

                $routeName = 'publishContentType' === $form->getClickedButton()->getName()
                    ? 'ezplatform.content_type.view'
                    : 'ezplatform.content_type.update';

                return $this->redirectToRoute($routeName, [
                    'contentTypeGroupId' => $group->id,
                    'contentTypeId' => $contentTypeDraft->id,
                ]);
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->render('@EzPlatformAdminUi/admin/content_type/edit.html.twig', [
            'content_type_group' => $group,
            'content_type' => $contentTypeDraft,
            'form' => $form->createView(),
        ]);
    }

    public function deleteAction(Request $request, ContentTypeGroup $group, ContentType $contentType): Response
    {
        $form = $this->createDeleteForm($group, $contentType);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function () use ($contentType) {
                $this->contentTypeService->deleteContentType($contentType);

                $this->notificationHandler->success(
                    $this->translator->trans(
                        /** @Desc("Content type '%name%' deleted.") */
                        'content_type.delete.success',
                        ['%name%' => $contentType->getName()],
                        'content_type'
                    )
                );
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirectToRoute('ezplatform.content_type_group.view', [
            'contentTypeGroupId' => $group->id,
        ]);
    }

    /**
     * Handles removing content types based on submitted form.
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws BadStateException
     * @throws TranslationInvalidArgumentException
     * @throws InvalidOptionsException
     * @throws UnauthorizedException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws \InvalidArgumentException
     */
    public function bulkDeleteAction(Request $request, ContentTypeGroup $group): Response
    {
        $form = $this->formFactory->deleteContentTypes(
            new ContentTypesDeleteData()
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $result = $this->submitHandler->handle($form, function (ContentTypesDeleteData $data) {
                foreach ($data->getContentTypes() as $contentTypeId => $selected) {
                    $contentType = $this->contentTypeService->loadContentType($contentTypeId);

                    $this->contentTypeService->deleteContentType($contentType);

                    $this->notificationHandler->success(
                        $this->translator->trans(
                            /** @Desc("Content type '%name%' deleted.") */
                            'content_type.delete.success',
                            ['%name%' => $contentType->getName()],
                            'content_type'
                        )
                    );
                }
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirect($this->generateUrl('ezplatform.content_type_group.view', ['contentTypeGroupId' => $group->id]));
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

        $languageCode = reset($this->languages);

        foreach ($this->languages as $prioritizedLanguage) {
            if (isset($contentType->names[$prioritizedLanguage])) {
                $languageCode = $prioritizedLanguage;
                break;
            }
        }

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

    /**
     * @param ContentType[] $contentTypes
     *
     * @return array
     */
    private function getContentTypesNumbers(array $contentTypes): array
    {
        $contentTypesNumbers = array_column($contentTypes, 'id');

        return array_combine($contentTypesNumbers, array_fill_keys($contentTypesNumbers, false));
    }
}
