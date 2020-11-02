<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\ChoiceList\Loader;

use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use EzSystems\EzPlatformAdminUi\Siteaccess\NonAdminSiteaccessResolver;
use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;

class SiteAccessChoiceLoader implements ChoiceLoaderInterface
{
    /** @var \EzSystems\EzPlatformAdminUi\Siteaccess\NonAdminSiteaccessResolver */
    private $nonAdminSiteaccessResolver;

    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    /** @var \eZ\Publish\API\Repository\Values\Content\Location|null */
    private $location;

    public function __construct(
        NonAdminSiteaccessResolver $nonAdminSiteaccessResolver,
        ConfigResolverInterface $configResolver,
        ?Location $location = null
    ) {
        $this->nonAdminSiteaccessResolver = $nonAdminSiteaccessResolver;
        $this->configResolver = $configResolver;
        $this->location = $location;
    }

    /**
     * Provides data in format:
     * [
     *     site_access => location_id,
     *     ...
     * ].
     *
     * @return int[]
     */
    public function getChoiceList(): array
    {
        $siteAccesses = $this->location === null
            ? $this->nonAdminSiteaccessResolver->getSiteaccesses()
            : $this->nonAdminSiteaccessResolver->getSiteaccessesForLocation($this->location);

        $parameterName = 'content.tree_root.location_id';
        $defaultTreeRootLocationId = $this->configResolver->getParameter($parameterName);

        $data = [];
        foreach ($siteAccesses as $siteAccess) {
            $treeRootLocationId = $this->configResolver->hasParameter($parameterName, null, $siteAccess)
                ? $this->configResolver->getParameter($parameterName, null, $siteAccess)
                : $defaultTreeRootLocationId;

            $data[$siteAccess] = $treeRootLocationId;
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function loadChoiceList($value = null)
    {
        $choices = $this->getChoiceList();

        return new ArrayChoiceList($choices, $value);
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
