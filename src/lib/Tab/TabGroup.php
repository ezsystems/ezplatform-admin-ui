<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab;

class TabGroup
{
    /** @var string */
    protected $identifier;

    /** @var TabInterface[] */
    protected $tabs;

    /**
     * @param string $name
     * @param array $tabs
     */
    public function __construct(string $name, array $tabs = [])
    {
        $this->identifier = $name;
        $this->tabs = $tabs;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @param string $identifier
     */
    public function setIdentifier(string $identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * @return TabInterface[]
     */
    public function getTabs(): array
    {
        return $this->tabs;
    }

    /**
     * @param TabInterface[] $tabs
     */
    public function setTabs(array $tabs)
    {
        $this->tabs = $tabs;
    }

    /**
     * @param TabInterface $tab
     */
    public function addTab(TabInterface $tab)
    {
        $this->tabs[$tab->getIdentifier()] = $tab;
    }

    /**
     * @param string $identifier
     */
    public function removeTab(string $identifier)
    {
        if (!isset($this->tabs[$identifier])) {
            throw new \InvalidArgumentException(sprintf('Could not find a tab identified as "%s".', $identifier));
        }

        unset($this->tabs[$identifier]);
    }
}
