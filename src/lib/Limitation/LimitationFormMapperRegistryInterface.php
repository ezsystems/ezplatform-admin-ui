<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Limitation;

/**
 * Interface for Limitation form mappers registry.
 */
interface LimitationFormMapperRegistryInterface
{
    /**
     * @return LimitationFormMapperInterface[]
     */
    public function getMappers();

    /**
     * Returns mapper corresponding to given Limitation identifier.
     *
     * @throws \InvalidArgumentException if no mapper exists for $limitationIdentifier
     *
     * @return LimitationFormMapperInterface
     */
    public function getMapper($limitationIdentifier);

    /**
     * Checks if a mapper exists for given Limitation identifier.
     *
     * @param string $limitationIdentifier
     *
     * @return bool
     */
    public function hasMapper($limitationIdentifier);

    /**
     * @param LimitationFormMapperInterface $mapper
     * @param string $limitationIdentifier limitation identifier the mapper is meant for
     */
    public function addMapper(LimitationFormMapperInterface $mapper, $limitationIdentifier);
}

class_alias(
    LimitationFormMapperRegistryInterface::class,
    \EzSystems\RepositoryForms\Limitation\LimitationFormMapperRegistryInterface::class
);
