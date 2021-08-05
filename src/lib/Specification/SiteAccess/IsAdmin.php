<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Specification\SiteAccess;

use eZ\Publish\Core\MVC\Symfony\SiteAccess;
use Ibexa\AdminUi\Exception\InvalidArgumentException;
use Ibexa\AdminUi\Specification\AbstractSpecification;
use Ibexa\Bundle\AdminUi\IbexaAdminUiBundle;

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

        return in_array($item->name, $this->siteAccessGroups[IbexaAdminUiBundle::ADMIN_GROUP_NAME], true);
    }
}

class_alias(IsAdmin::class, 'EzSystems\EzPlatformAdminUi\Specification\SiteAccess\IsAdmin');
