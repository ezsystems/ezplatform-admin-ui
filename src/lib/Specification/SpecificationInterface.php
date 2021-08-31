<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Specification;

interface SpecificationInterface
{
    /**
     * @param $item
     *
     * @return bool
     */
    public function isSatisfiedBy($item): bool;

    /**
     * @param SpecificationInterface $other
     *
     * @return SpecificationInterface
     */
    public function and(SpecificationInterface $other): SpecificationInterface;

    /**
     * @param SpecificationInterface $other
     *
     * @return SpecificationInterface
     */
    public function or(SpecificationInterface $other): SpecificationInterface;

    /**
     * @return SpecificationInterface
     */
    public function not(): SpecificationInterface;
}
