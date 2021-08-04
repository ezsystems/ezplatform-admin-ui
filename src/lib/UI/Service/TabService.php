<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\UI\Service;

use Ibexa\AdminUi\Tab\TabGroup;
use Ibexa\Contracts\AdminUi\Tab\TabInterface;
use Ibexa\AdminUi\Tab\TabRegistry;

class TabService
{
    /** @var TabRegistry */
    protected $tabRegistry;

    /**
     * @param TabRegistry $tabRegistry
     */
    public function __construct(TabRegistry $tabRegistry)
    {
        $this->tabRegistry = $tabRegistry;
    }

    /**
     * @param string $groupIdentifier
     *
     * @return TabGroup
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
     * @return TabInterface
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

class_alias(TabService::class, 'EzSystems\EzPlatformAdminUi\UI\Service\TabService');
