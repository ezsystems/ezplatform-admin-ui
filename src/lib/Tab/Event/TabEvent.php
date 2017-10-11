<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab\Event;

use EzSystems\EzPlatformAdminUi\Tab\TabInterface;
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
