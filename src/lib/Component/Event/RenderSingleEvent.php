<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Component\Event;

use EzSystems\EzPlatformAdminUi\Component\Registry;
use EzSystems\EzPlatformAdminUi\Component\Renderable;
use Symfony\Component\EventDispatcher\Event;

class RenderSingleEvent extends Event
{
    const NAME = 'ezplatform_admin_ui.component.render_single';

    /** @var Registry */
    private $registry;

    /** @var string */
    private $groupName;

    /** @var string */
    private $serviceId;

    /** @var array */
    private $parameters;

    /**
     * @param Registry $registry
     * @param string $groupName
     * @param array $parameters
     */
    public function __construct(Registry $registry, string $groupName, string $serviceId, array $parameters = [])
    {
        $this->registry = $registry;
        $this->groupName = $groupName;
        $this->serviceId = $serviceId;
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
     * @return string
     */
    public function getName(): string
    {
        return $this->serviceId;
    }

    /**
     * @return Renderable
     */
    public function getComponent(): Renderable
    {
        $group = $this->registry->getComponents($this->getGroupName());

        return $group[$this->serviceId];
    }

    /**
     * @param Renderable $component
     */
    public function setComponent(Renderable $component)
    {
        $this->registry->addComponent($this->getGroupName(), $this->getName(), $component);
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }
}
