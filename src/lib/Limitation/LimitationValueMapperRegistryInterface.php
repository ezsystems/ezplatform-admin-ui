<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Limitation;

/**
 * Interface for Limitation value mappers registry.
 */
interface LimitationValueMapperRegistryInterface
{
    /**
     * Returns all available mappers.
     *
     * @return LimitationValueMapperInterface[]
     */
    public function getMappers();

    /**
     * Returns mapper corresponding to given Limitation Type.
     *
     * @throws \EzSystems\EzPlatformAdminUi\Exception\ValueMapperNotFoundException if no mapper exists for $limitationType
     *
     * @param string $limitationType
     *
     * @return LimitationValueMapperInterface
     */
    public function getMapper($limitationType);

    /**
     * Checks if a mapper exists for given Limitation Type.
     *
     * @param string $limitationType
     *
     * @return bool
     */
    public function hasMapper($limitationType);

    /**
     * Register mapper.
     *
     * @param LimitationValueMapperInterface $mapper
     * @param string $limitationType limitation identifier the mapper is meant for
     */
    public function addMapper(LimitationValueMapperInterface $mapper, $limitationType);
}

class_alias(
    LimitationValueMapperRegistryInterface::class,
    \EzSystems\RepositoryForms\Limitation\LimitationValueMapperRegistryInterface::class
);
