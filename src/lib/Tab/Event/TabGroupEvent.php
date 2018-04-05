<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab\Event;

use EzSystems\EzPlatformAdminUi\Tab\TabGroup;
use Symfony\Component\EventDispatcher\Event;

class TabGroupEvent extends Event
{
    /** @var TabGroup */
    private $data;

    /** @var array */
    private $parameters;

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

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     */
    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }
}
