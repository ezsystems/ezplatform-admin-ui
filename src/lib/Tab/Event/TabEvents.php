<?php
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab\Event;


class TabEvents
{
    /**
     * Happens just before rendering tabs group.
     */
    const TAB_GROUP_PRE_RENDER = 'ezplatform.tab.group.pre_render';

    /**
     * Happens just before rendering tab.
     */
    const TAB_PRE_RENDER = 'ezplatform.tab.pre_render';
}
