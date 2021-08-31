<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Specification;

class NotSpecification extends AbstractSpecification
{
    /**
     * @var SpecificationInterface
     */
    private $specification;

    /**
     * @param SpecificationInterface $specification
     */
    public function __construct(SpecificationInterface $specification)
    {
        $this->specification = $specification;
    }

    /**
     * @param $item
     *
     * @return bool
     */
    public function isSatisfiedBy($item): bool
    {
        return !$this->specification->isSatisfiedBy($item);
    }
}
