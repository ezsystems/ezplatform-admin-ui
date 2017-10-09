<?php
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab\Event;


use EzSystems\EzPlatformAdminUi\Tab\TabGroup;
use Symfony\Component\EventDispatcher\Event;

class TabGroupEvent extends Event
{
    /** @var TabGroup */
    private $data;

    /**
     * @return TabGroup
     */
    public function getData(): TabGroup
    {
        return $this->data;
    }

    /**
     * @param TabGroup $data
     */
    public function setData(TabGroup $data)
    {
        $this->data = $data;
    }
}
