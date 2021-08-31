<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab\Event;

use EzSystems\EzPlatformAdminUi\Tab\TabInterface;
use Symfony\Contracts\EventDispatcher\Event;

class TabEvent extends Event
{
    /** @var \EzSystems\EzPlatformAdminUi\Tab\TabInterface */
    private $data;

    /** @var array */
    private $parameters;

    /**
     * @return \EzSystems\EzPlatformAdminUi\Tab\TabInterface
     */
    public function getData(): TabInterface
    {
        return $this->data;
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Tab\TabInterface $data
     */
    public function setData(TabInterface $data)
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
