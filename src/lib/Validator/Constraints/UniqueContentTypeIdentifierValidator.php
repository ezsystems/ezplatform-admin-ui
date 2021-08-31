<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Validator\Constraints;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use EzSystems\EzPlatformAdminUi\Form\Data\ContentTypeData;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Will check if ContentType identifier is not already used in the content repository.
 */
class UniqueContentTypeIdentifierValidator extends ConstraintValidator
{
    /**
     * @var \eZ\Publish\API\Repository\ContentTypeService
     */
    private $contentTypeService;

    public function __construct(ContentTypeService $contentTypeService)
    {
        $this->contentTypeService = $contentTypeService;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\ContentTypeData $value The value that should be validated
     * @param \Symfony\Component\Validator\Constraint|UniqueFieldDefinitionIdentifier $constraint The constraint for the validation
     *
     * @api
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof ContentTypeData || $value->identifier === null) {
            return;
        }

        try {
            $contentType = $this->contentTypeService->loadContentTypeByIdentifier($value->identifier);
            // It's of course OK to edit a draft of an existing ContentType :-)
            if ($contentType->id === $value->contentTypeDraft->id) {
                return;
            }

            $this->context->buildViolation($constraint->message)
                ->atPath('identifier')
                ->setParameter('%identifier%', $value->identifier)
                ->addViolation();
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }
}
