<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Limitation;

use eZ\Publish\API\Repository\Values\User\Limitation;
use InvalidArgumentException;

/**
 * Registry for Limitation form mappers.
 */
class LimitationFormMapperRegistry implements LimitationFormMapperRegistryInterface
{
    /**
     * Limitation form mappers, indexed by Limitation identifier.
     *
     * @var LimitationFormMapperInterface[]
     */
    private $limitationFormMappers = [];

    public function getMappers()
    {
        return $this->limitationFormMappers;
    }

    public function addMapper(LimitationFormMapperInterface $mapper, $fieldTypeIdentifier)
    {
        $this->limitationFormMappers[$fieldTypeIdentifier] = $mapper;
    }

    /**
     * Returns mapper corresponding to given Limitation identifier.
     *
     * @param string $limitationIdentifier
     *
     * @throws \InvalidArgumentException if no mapper exists for $fieldTypeIdentifier
     *
     * @return LimitationFormMapperInterface
     */
    public function getMapper($limitationIdentifier)
    {
        if (!$this->hasMapper($limitationIdentifier)) {
            throw new InvalidArgumentException("No LimitationFormMapper found for '$limitationIdentifier'");
        }

        return $this->limitationFormMappers[$limitationIdentifier];
    }

    /**
     * Checks if a mapper exists for given Limitation identifier.
     *
     * @param string $limitationIdentifier
     *
     * @return bool
     */
    public function hasMapper($limitationIdentifier)
    {
        return isset($this->limitationFormMappers[$limitationIdentifier]);
    }
}
