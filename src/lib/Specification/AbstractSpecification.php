<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Specification;

abstract class AbstractSpecification implements SpecificationInterface
{
    /**
     * @param $item
     *
     * @return bool
     */
    abstract public function isSatisfiedBy($item): bool;

    /**
     * @param SpecificationInterface $other
     *
     * @return SpecificationInterface
     */
    public function and(SpecificationInterface $other): SpecificationInterface
    {
        return new AndSpecification($this, $other);
    }

    /**
     * @param SpecificationInterface $other
     *
     * @return SpecificationInterface
     */
    public function or(SpecificationInterface $other): SpecificationInterface
    {
        return new OrSpecification($this, $other);
    }

    /**
     * @return SpecificationInterface
     */
    public function not(): SpecificationInterface
    {
        return new NotSpecification($this);
    }
}
