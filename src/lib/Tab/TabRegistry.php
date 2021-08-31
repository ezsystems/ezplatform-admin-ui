<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab;

class TabRegistry
{
    /** @var TabGroup[] */
    protected $tabGroups;

    /**
     * @param string $group
     *
     * @return TabInterface[]
     */
    public function getTabsByGroupName(string $group): array
    {
        return $this->getTabGroup($group)->getTabs();
    }

    /**
     * @param string $group
     *
     * @return TabGroup
     */
    public function getTabGroup(string $group): TabGroup
    {
        if (!isset($this->tabGroups[$group])) {
            throw new \InvalidArgumentException(sprintf('Could not find the requested group named "%s". Did you tag the service?', $group));
        }

        return $this->tabGroups[$group];
    }

    /**
     * @param string $name
     * @param string $group
     *
     * @return TabInterface
     */
    public function getTabFromGroup(string $name, string $group): TabInterface
    {
        if (!isset($this->tabGroups[$group])) {
            throw new \InvalidArgumentException(sprintf('Could not find the requested group named "%s". Did you tag the service?', $group));
        }

        foreach ($this->tabGroups[$group]->getTabs() as $tab) {
            if ($tab->getName() === $name) {
                return $tab;
            }
        }

        throw new \InvalidArgumentException(sprintf('Could not find the requested tab "%s" from group "%s". Did you tag the service?', $name, $group));
    }

    /**
     * @param TabGroup $group
     */
    public function addTabGroup(TabGroup $group)
    {
        $this->tabGroups[$group->getIdentifier()] = $group;
    }

    /**
     * @param TabInterface $tab
     * @param string $group
     */
    public function addTab(TabInterface $tab, string $group)
    {
        if (!isset($this->tabGroups[$group])) {
            $this->tabGroups[$group] = new TabGroup($group, []);
        }

        $this->tabGroups[$group]->addTab($tab);
    }
}
