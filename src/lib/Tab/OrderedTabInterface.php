<?php

declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab;


/**
 * Ordered Tab interface needs to be implemented
 * by tabs, which needs to have specific order
 * when being rendered.
 */
interface OrderedTabInterface
{
    /**
     * Get the order of this tab.
     *
     * @return int
     */
    public function getOrder(): int;
}
