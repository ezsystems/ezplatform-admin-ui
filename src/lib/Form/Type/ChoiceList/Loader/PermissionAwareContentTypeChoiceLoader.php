<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\ChoiceList\Loader;

use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\Values\User\Limitation\ContentTypeLimitation;
use EzSystems\EzPlatformAdminUi\Permission\PermissionCheckerInterface;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;

/**
 * @deprecated since version 2.5, to be removed in 3.0. Use '\EzSystems\EzPlatformAdminUi\Form\Type\ChoiceList\Loader\ContentCreateContentTypeChoiceLoader' instead.
 */
class PermissionAwareContentTypeChoiceLoader implements ChoiceLoaderInterface
{
    /** @var \Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface */
    private $decorated;

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \EzSystems\EzPlatformAdminUi\Permission\PermissionCheckerInterface */
    private $permissionChecker;

    /** @var string */
    private $module;

    /** @var string */
    private $function;

    /**
     * @param \eZ\Publish\API\Repository\PermissionResolver $permissionResolver
     * @param \EzSystems\EzPlatformAdminUi\Permission\PermissionCheckerInterface $permissionChecker
     * @param \EzSystems\EzPlatformAdminUi\Form\Type\ChoiceList\Loader\ContentTypeChoiceLoader $decorated
     * @param string $module
     * @param string $function
     */
    public function __construct(
        PermissionResolver $permissionResolver,
        PermissionCheckerInterface $permissionChecker,
        ContentTypeChoiceLoader $decorated,
        string $module,
        string $function
    ) {
        $this->decorated = $decorated;
        $this->permissionResolver = $permissionResolver;
        $this->permissionChecker = $permissionChecker;
        $this->module = $module;
        $this->function = $function;
    }

    /**
     * {@inheritdoc}
     */
    public function loadChoiceList($value = null)
    {
        $hasAccess = $this->permissionResolver->hasAccess($this->module, $this->function);
        if (!is_bool($hasAccess)) {
            $restrictedContentTypesIds = $this->permissionChecker->getRestrictions($hasAccess, ContentTypeLimitation::class);
        }
        $contentTypesGroups = $this->decorated->getChoiceList();

        if (empty($restrictedContentTypesIds)) {
            return new ArrayChoiceList($contentTypesGroups, $value);
        }

        foreach ($contentTypesGroups as $group => $contentTypes) {
            $contentTypesGroups[$group] = array_filter($contentTypes, function (ContentType $contentType) use ($restrictedContentTypesIds) {
                return in_array($contentType->id, $restrictedContentTypesIds);
            });
        }

        return new ArrayChoiceList($contentTypesGroups, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function loadChoicesForValues(array $values, $value = null)
    {
        // Optimize
        $values = array_filter($values);
        if (empty($values)) {
            return [];
        }

        // If no callable is set, values are the same as choices
        if (null === $value) {
            return $values;
        }

        return $this->loadChoiceList($value)->getChoicesForValues($values);
    }

    /**
     * {@inheritdoc}
     */
    public function loadValuesForChoices(array $choices, $value = null)
    {
        // Optimize
        $choices = array_filter($choices);
        if (empty($choices)) {
            return [];
        }

        // If no callable is set, choices are the same as values
        if (null === $value) {
            return $choices;
        }

        return $this->loadChoiceList($value)->getValuesForChoices($choices);
    }
}
