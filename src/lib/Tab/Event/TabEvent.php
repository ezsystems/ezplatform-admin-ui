<?php
declare(strict_types=1);

namespace EzPlatformAdminUi\Tab\Event;


use EzPlatformAdminUi\Tab\TabInterface;
use Symfony\Component\EventDispatcher\Event;

class TabEvent extends Event
{
    /** @var TabInterface */
    private $data;

    /**
     * @return TabInterface
     */
    public function getData(): TabInterface
    {
        return $this->data;
    }

    /**
     * @param TabInterface $data
     */
    public function setData(TabInterface $data)
    {
        $this->data = $data;
    }
}
