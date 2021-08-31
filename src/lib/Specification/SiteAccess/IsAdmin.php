<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Specification\SiteAccess;

use eZ\Publish\Core\MVC\Symfony\SiteAccess;
use EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException;
use EzSystems\EzPlatformAdminUi\Specification\AbstractSpecification;
use EzSystems\EzPlatformAdminUiBundle\EzPlatformAdminUiBundle;

class IsAdmin extends AbstractSpecification
{
    /** @var array */
    private $siteAccessGroups;

    /**
     * @param array $siteAccessGroups
     */
    public function __construct(array $siteAccessGroups)
    {
        $this->siteAccessGroups = $siteAccessGroups;
    }

    /**
     * @param $item
     *
     * @return bool
     *
     * @throws \EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException
     */
    public function isSatisfiedBy($item): bool
    {
        if (!$item instanceof SiteAccess) {
            throw new InvalidArgumentException($item, sprintf('Must be an instance of %s', SiteAccess::class));
        }

        return in_array($item->name, $this->siteAccessGroups[EzPlatformAdminUiBundle::ADMIN_GROUP_NAME], true);
    }
}
