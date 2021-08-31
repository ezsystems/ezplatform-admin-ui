<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Service;

use EzSystems\EzPlatformAdminUi\Tab\TabGroup;
use EzSystems\EzPlatformAdminUi\Tab\TabInterface;
use EzSystems\EzPlatformAdminUi\Tab\TabRegistry;

class TabService
{
    /** @var \EzSystems\EzPlatformAdminUi\Tab\TabRegistry */
    protected $tabRegistry;

    /**
     * @param \EzSystems\EzPlatformAdminUi\Tab\TabRegistry $tabRegistry
     */
    public function __construct(TabRegistry $tabRegistry)
    {
        $this->tabRegistry = $tabRegistry;
    }

    /**
     * @param string $groupIdentifier
     *
     * @return \EzSystems\EzPlatformAdminUi\Tab\TabGroup
     */
    public function getTabGroup(string $groupIdentifier): TabGroup
    {
        return $this->tabRegistry->getTabGroup($groupIdentifier);
    }

    /**
     * @param string $groupIdentifier
     *
     * @return array
     */
    public function getTabsFromGroup(string $groupIdentifier): array
    {
        $tabGroup = $this->tabRegistry->getTabGroup($groupIdentifier);

        return $tabGroup->getTabs();
    }

    /**
     * @param string $tabIdentifier
     * @param string $groupIdentifier
     *
     * @return \EzSystems\EzPlatformAdminUi\Tab\TabInterface
     */
    public function getTabFromGroup(string $tabIdentifier, string $groupIdentifier): TabInterface
    {
        $tabs = $this->getTabsFromGroup($groupIdentifier);

        if (!isset($tabs[$tabIdentifier])) {
            throw new \InvalidArgumentException(sprintf('There is no "%s" tab assigned to "%s" group.', $tabIdentifier,
                $groupIdentifier));
        }

        return $tabs[$tabIdentifier];
    }
}
