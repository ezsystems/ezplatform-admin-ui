<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Limitation\Mapper;

use eZ\Publish\API\Repository\Values\User\Limitation;
use EzSystems\EzPlatformAdminUi\Limitation\LimitationValueMapperInterface;

class SiteAccessLimitationMapper extends MultipleSelectionBasedMapper implements LimitationValueMapperInterface
{
    /**
     * @var array
     */
    private $siteAccessList;

    public function __construct(array $siteAccessList)
    {
        $this->siteAccessList = $siteAccessList;
    }

    protected function getSelectionChoices()
    {
        $siteAccesses = [];
        foreach ($this->siteAccessList as $sa) {
            $siteAccesses[$this->getSiteAccessKey($sa)] = $sa;
        }

        return $siteAccesses;
    }

    public function mapLimitationValue(Limitation $limitation)
    {
        $values = [];
        foreach ($this->siteAccessList as $sa) {
            if (in_array($this->getSiteAccessKey($sa), $limitation->limitationValues)) {
                $values[] = $sa;
            }
        }

        return $values;
    }

    private function getSiteAccessKey($sa)
    {
        return sprintf('%u', crc32($sa));
    }
}
