<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Validator\Constraints;

use eZ\Publish\API\Repository\Exceptions\InvalidArgumentException;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use EzSystems\EzPlatformAdminUi\Specification\Location\IsWithinCopySubtreeLimit;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class LocationIsWithinCopySubtreeLimitValidator extends ConstraintValidator
{
    /** @var \eZ\Publish\API\Repository\SearchService */
    private $searchService;

    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    public function __construct(
        SearchService $searchService,
        ConfigResolverInterface $configResolver
    ) {
        $this->searchService = $searchService;
        $this->configResolver = $configResolver;
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
            return;
        }

        $isWithinCopySubtreeLimit = new IsWithinCopySubtreeLimit(
            $this->configResolver->getParameter('subtree_operations.copy_subtree.limit'),
            $this->searchService
        );
        try {
            if (!$isWithinCopySubtreeLimit->isSatisfiedBy($location)) {
                $this
                    ->context
                    ->buildViolation($constraint->message)
                    ->setParameter(
                        '%currentLimit%',
                        (string)$this->configResolver->getParameter('subtree_operations.copy_subtree.limit')
                    )
                    ->addViolation();
            }
        } catch (InvalidArgumentException $e) {
            $this->context->addViolation($e->getMessage());
        }
    }
}
