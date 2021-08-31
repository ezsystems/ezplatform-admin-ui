<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Limitation;

use EzSystems\EzPlatformAdminUi\Exception\ValueMapperNotFoundException;

/**
 * Registry for Limitation value mappers.
 */
class LimitationValueMapperRegistry implements LimitationValueMapperRegistryInterface
{
    /**
     * @var LimitationValueMapperInterface[]
     */
    private $limitationValueMappers;

    /**
     * LimitationValueMapperRegistry constructor.
     *
     * @param LimitationValueMapperInterface[] $limitationValueMappers
     */
    public function __construct(array $limitationValueMappers = [])
    {
        $this->limitationValueMappers = $limitationValueMappers;
    }

    public function getMappers()
    {
        return $this->limitationValueMappers;
    }

    public function getMapper($limitationType)
    {
        if (!$this->hasMapper($limitationType)) {
            throw new ValueMapperNotFoundException($limitationType);
        }

        return $this->limitationValueMappers[$limitationType];
    }

    public function hasMapper($limitationType)
    {
        return isset($this->limitationValueMappers[$limitationType]);
    }

    public function addMapper(LimitationValueMapperInterface $mapper, $limitationType)
    {
        $this->limitationValueMappers[$limitationType] = $mapper;
    }
}
