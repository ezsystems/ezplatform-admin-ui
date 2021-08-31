<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab\Event;

use EzSystems\EzPlatformAdminUi\Tab\TabGroup;
use Symfony\Contracts\EventDispatcher\Event;

class TabGroupEvent extends Event
{
    /** @var \EzSystems\EzPlatformAdminUi\Tab\TabGroup */
    private $data;

    /** @var array */
    private $parameters;

    /**
     * @return \EzSystems\EzPlatformAdminUi\Tab\TabGroup
     */
    public function getData(): TabGroup
    {
        return $this->data;
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Tab\TabGroup $data
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
