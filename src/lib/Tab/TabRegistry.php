<?php

declare(strict_types=1);

namespace EzPlatformAdminUi\Tab;


class TabRegistry
{
    /** @var TabGroup[] */
    protected $tabGroups;

    /**
     * @param string $group
     *
     * @return TabInterface[]
     */
    public function getTabsByGroupName(string $group) : array
    {
        return $this->getTabGroup($group)->getTabs();
    }

    /**
     * @param string $group
     *
     * @return TabGroup
     */
    public function getTabGroup(string $group) : TabGroup
    {
        if (!isset($this->tabGroups[$group])) {
            throw new \InvalidArgumentException(sprintf('Requested group named "%s" is not found. Did you forget to tag the service?', $group));
        }

        return $this->tabGroups[$group];
    }

    /**
     * @param string $name
     * @param string $group
     *
     * @return TabInterface
     */
    public function getTabFromGroup(string $name, string $group) : TabInterface
    {
        if (!isset($this->tabGroups[$group])) {
            throw new \InvalidArgumentException(sprintf('Requested group named "%s" is not found. Did you forget to tag the service?', $group));
        }

        foreach ($this->tabGroups[$group]->getTabs() as $tab) {
            if ($tab->getName() === $name) {
                return $tab;
            }
        }

        throw new \InvalidArgumentException(sprintf('Requested tab "%s" from group "%s" is not found. Did you forget to tag the service?', $name, $group));
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
