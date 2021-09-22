<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\AdminUi\Controller;

use Exception;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Exceptions\InvalidArgumentException;
use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\API\Repository\Values\ContentType\ContentTypeDraft;
use eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup;
use EzSystems\EzPlatformRest\Exceptions;
use EzSystems\EzPlatformRest\Message;
use EzSystems\EzPlatformRest\Server\Controller as RestController;
use EzSystems\EzPlatformRest\Server\Values;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class FieldDefinitionController extends RestController
{
    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface */
    private $urlGenerator;

    public function __construct(ContentTypeService $contentTypeService, UrlGeneratorInterface $urlGenerator)
    {
        $this->contentTypeService = $contentTypeService;
        $this->urlGenerator = $urlGenerator;
    }

    public function addFieldDefinitionAction(
        Request $request,
        ContentTypeGroup $group,
        ContentTypeDraft $contentTypeDraft,
        Language $language,
        ?Language $baseLanguage = null
    ): RedirectResponse {
        /** @var \EzSystems\EzPlatformAdminUi\REST\Value\ContentType\FieldDefinitionCreate $input */
        $input = $this->inputDispatcher->parse(
            new Message(
                ['Content-Type' => $request->headers->get('Content-Type')],
                $request->getContent()
            )
        );

        $fieldDefinitionCreateStruct = $this->contentTypeService->newFieldDefinitionCreateStruct(
            uniqid('field_'),
            $input->fieldTypeIdentifier
        );

        $fieldDefinitionCreateStruct->fieldGroup = $input->fieldGroupIdentifier;
        $fieldDefinitionCreateStruct->names = [
            $language->languageCode => "New $fieldDefinitionCreateStruct->fieldTypeIdentifier field definition",
        ];

        if (!$contentTypeDraft->fieldDefinitions->isEmpty()) {
            $fieldDefinitionCreateStruct->position = $contentTypeDraft->fieldDefinitions->last()->position;
        } else {
            $fieldDefinitionCreateStruct->position = 0;
        }

        $this->contentTypeService->addFieldDefinition(
            $contentTypeDraft,
            $fieldDefinitionCreateStruct
        );

        return new RedirectResponse(
            $this->urlGenerator->generate(
                'ibexa.content_type.field_definition_form',
                [
                    'fieldDefinitionIdentifier' => $fieldDefinitionCreateStruct->identifier,
                    'contentTypeGroupId' => $group->id,
                    'contentTypeId' => $contentTypeDraft->id,
                    'toLanguageCode' => $language->languageCode,
                    'fromLanguageCode' => $baseLanguage ? $baseLanguage->languageCode : null,
                ]
            )
        );
    }

    public function removeFieldDefinitionAction(
        Request $request,
        ContentTypeGroup $group,
        ContentTypeDraft $contentTypeDraft
    ): Values\OK {
        /** @var \EzSystems\EzPlatformAdminUi\REST\Value\ContentType\FieldDefinitionDelete $input */
        $input = $this->inputDispatcher->parse(
            new Message(
                ['Content-Type' => $request->headers->get('Content-Type')],
                $request->getContent()
            )
        );

        $this->repository->beginTransaction();
        try {
            foreach ($input->fieldDefinitionIdentifiers as $identifier) {
                if (!$contentTypeDraft->fieldDefinitions->has($identifier)) {
                    throw new Exceptions\NotFoundException("No field definition with $identifier found");
                }

                $this->contentTypeService->removeFieldDefinition(
                    $contentTypeDraft,
                    $contentTypeDraft->fieldDefinitions->get($identifier)
                );
            }

            $this->repository->commit();
        } catch (InvalidArgumentException $e) {
            $this->repository->rollback();

            throw new Exceptions\ForbiddenException($e->getMessage());
        } catch (Exception $e) {
            $this->repository->rollback();

            throw $e;
        }

        return new Values\OK();
    }

    public function reorderFieldDefinitionsAction(
        Request $request,
        ContentTypeGroup $group,
        ContentTypeDraft $contentTypeDraft
    ): Values\OK {
        /** @var \EzSystems\EzPlatformAdminUi\REST\Value\ContentType\FieldDefinitionReorder $input */
        $input = $this->inputDispatcher->parse(
            new Message(
                ['Content-Type' => $request->headers->get('Content-Type')],
                $request->getContent()
            )
        );

        $this->repository->beginTransaction();
        try {
            foreach ($input->fieldDefinitionIdentifiers as $position => $identifier) {
                $updateStruct = $this->contentTypeService->newFieldDefinitionUpdateStruct();
                $updateStruct->position = $position;

                $this->contentTypeService->updateFieldDefinition(
                    $contentTypeDraft,
                    $contentTypeDraft->getFieldDefinition($identifier),
                    $updateStruct
                );
            }

            $this->repository->commit();
        } catch (InvalidArgumentException $e) {
            $this->repository->rollback();

            throw new Exceptions\ForbiddenException($e->getMessage());
        }

        return new Values\OK();
    }
}
