<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Specification;

class AndSpecification extends AbstractSpecification
{
    /** @var SpecificationInterface */
    private $one;

    /** @var SpecificationInterface */
    private $two;

    /**
     * @param SpecificationInterface $one
     * @param SpecificationInterface $two
     */
    public function __construct(SpecificationInterface $one, SpecificationInterface $two)
    {
        $this->one = $one;
        $this->two = $two;
    }

    /**
     * @param $item
     *
     * @return bool
     */
    public function isSatisfiedBy($item): bool
    {
        return $this->one->isSatisfiedBy($item) && $this->two->isSatisfiedBy($item);
    }
}
