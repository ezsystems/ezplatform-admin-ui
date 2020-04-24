<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Limitation\Mapper;

use eZ\Publish\API\Repository\Values\User\Limitation;
use eZ\Publish\Core\MVC\Symfony\SiteAccess;
use eZ\Publish\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use EzSystems\EzPlatformAdminUi\Limitation\LimitationValueMapperInterface;

class SiteAccessLimitationMapper extends MultipleSelectionBasedMapper implements LimitationValueMapperInterface
{
    /** @var \eZ\Publish\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface */
    private $siteAccessService;

    public function __construct(
        SiteAccessServiceInterface $siteAccessService
    ) {
        $this->siteAccessService = $siteAccessService;
    }

    protected function getSelectionChoices()
    {
        $siteAccesses = [];
        foreach ($this->siteAccessService->getAll() as $sa) {
            $siteAccesses[$this->getSiteAccessKey($sa->name)] = $this->getSiteAccessName($sa);
        }

        return $siteAccesses;
    }

    public function mapLimitationValue(Limitation $limitation)
    {
        $values = [];
        foreach ($this->siteAccessService->getAll() as $sa) {
            if (in_array($this->getSiteAccessKey($sa->name), $limitation->limitationValues)) {
                $values[] = $this->getSiteAccessName($sa);
            }
        }

        return $values;
    }

    private function getSiteAccessKey($sa)
    {
        return sprintf('%u', crc32($sa));
    }

    private function getSiteAccessName(SiteAccess $sa)
    {
        if (empty($sa->siteFactoryName)) {
            return $sa->name;
        } else {
            return $sa->siteFactoryName;
        }
    }
}
