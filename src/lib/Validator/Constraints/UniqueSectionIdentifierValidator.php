<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Validator\Constraints;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\SectionService;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Section\SectionUpdateData;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Will check if ContentType identifier is not already used in the content repository.
 */
class UniqueSectionIdentifierValidator extends ConstraintValidator
{
    /**
     * @var SectionService
     */
    private $sectionService;

    public function __construct(SectionService $sectionService)
    {
        $this->sectionService = $sectionService;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param SectionUpdateData $value The value that should be validated
     * @param Constraint|UniqueFieldDefinitionIdentifier $constraint The constraint for the validation
     *
     * @api
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof SectionUpdateData) {
            return;
        }

        try {
            $section = $this->sectionService->loadSectionByIdentifier($value->identifier);
            if ($section->id == $value->getId()) {
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
