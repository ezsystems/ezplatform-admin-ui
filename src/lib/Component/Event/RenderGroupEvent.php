<?php
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Component\Event;

use EzSystems\EzPlatformAdminUi\Component\Registry;
use Symfony\Component\EventDispatcher\Event;

class RenderGroupEvent extends Event
{
    const NAME = 'ezplatform_admin_ui.component.render_group';

    /** @var Registry */
    private $registry;

    /** @var string */
    private $groupName;

    /** @var array */
    private $parameters;

    /**
     * @param Registry $registry
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