<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\ChoiceList\Loader;

use eZ\Publish\API\Repository\Values\Content\Location;
use EzSystems\EzPlatformAdminUi\Siteaccess\NonAdminSiteaccessResolver;
use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;

class SiteAccessChoiceLoader implements ChoiceLoaderInterface
{
    /** @var \EzSystems\EzPlatformAdminUi\Siteaccess\NonAdminSiteaccessResolver */
    private $nonAdminSiteaccessResolver;

    /** @var \eZ\Publish\API\Repository\Values\Content\Location|null */
    private $location;

    public function __construct(
        NonAdminSiteaccessResolver $nonAdminSiteaccessResolver,
        ?Location $location = null
    ) {
        $this->nonAdminSiteaccessResolver = $nonAdminSiteaccessResolver;
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

        $data = [];
        foreach ($siteAccesses as $siteAccess) {
            $data[$siteAccess] = $siteAccess;
        }

        return $data;
    }

    public function loadChoiceList($value = null)
    {
        $choices = $this->getChoiceList();

        return new ArrayChoiceList($choices, $value);
    }

    public function loadChoicesForValues(array $values, $value = null)
    {
        // Optimize
        $values = array_filter($values);
        if (empty($values)) {
            return [];
        }

        return $this->loadChoiceList($value)->getChoicesForValues($values);
    }

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
