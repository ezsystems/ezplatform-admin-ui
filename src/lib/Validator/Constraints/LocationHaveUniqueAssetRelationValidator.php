<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Validator\Constraints;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException;
use EzSystems\EzPlatformAdminUi\Specification\Content\ContentHaveUniqueRelation;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class LocationHaveUniqueAssetRelationValidator extends ConstraintValidator
{
    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /**
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     */
    public function __construct(ContentService $contentService)
    {
        $this->contentService = $contentService;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location The value that should be validated
     * @param \Symfony\Component\Validator\Constraint $constraint The constraint for the validation
     */
    public function validate($location, Constraint $constraint)
    {
        if (null === $location) {
            $this->context->addViolation($constraint->message);

            return;
        }

        $haveUniqueRelation = new ContentHaveUniqueRelation($this->contentService);
        try {
            if (!$haveUniqueRelation->isSatisfiedBy($location->getContent())) {
                $this->context->addViolation($constraint->message);
            }
        } catch (InvalidArgumentException $e) {
            $this->context->addViolation($e->getMessage());
        } catch (UnauthorizedException $e) {
            $this->context->addViolation($e->getMessage());
        }
    }
}
