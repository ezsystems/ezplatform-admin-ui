<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Validator\Constraints;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use EzSystems\EzPlatformAdminUi\Specification\Location\IsContainer;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class LocationIsContainerValidator extends ConstraintValidator
{
    /** @var \EzSystems\EzPlatformAdminUi\Service\ContentTypeService */
    private $contentTypeService;

    /**
     * @param \EzSystems\EzPlatformAdminUi\Service\ContentTypeService $contentTypeService
     */
    public function __construct(ContentTypeService $contentTypeService)
    {
        $this->contentTypeService = $contentTypeService;
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

        $isContainer = new IsContainer($this->contentTypeService);
        try {
            if (!$isContainer->isSatisfiedBy($location)) {
                $this->context->addViolation($constraint->message);
            }
        } catch (NotFoundException $e) {
            $this->context->addViolation($e->getMessage());
        }
    }
}
