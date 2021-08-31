<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Component\Event;

use EzSystems\EzPlatformAdminUi\Component\Registry;
use Symfony\Contracts\EventDispatcher\Event;

class RenderGroupEvent extends Event
{
    const NAME = 'ezplatform_admin_ui.component.render_group';

    /** @var \EzSystems\EzPlatformAdminUi\Component\Registry */
    private $registry;

    /** @var string */
    private $groupName;

    /** @var array */
    private $parameters;

    /**
     * @param \EzSystems\EzPlatformAdminUi\Component\Registry $registry
     * @param string $groupName
     * @param array $parameters
     */
    public function __construct(Registry $registry, string $groupName, array $parameters = [])
    {
        $this->registry = $registry;
        $this->groupName = $groupName;
        $this->parameters = $parameters;
    }

    /**
     * @return string
     */
    public function getGroupName(): string
    {
        return $this->groupName;
    }

    /**
     * @return array
     */
    public function getComponents(): array
    {
        return $this->registry->getComponents($this->getGroupName());
    }

    /**
     * @param array $components
     */
    public function setComponents(array $components)
    {
        $this->registry->setComponents($this->getGroupName(), $components);
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }
}
