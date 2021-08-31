<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab\Event;

class TabEvents
{
    /**
     * Happens just after tabs group creation.
     */
    const TAB_GROUP_INITIALIZE = 'ezplatform.tab.group.initialize';

    /**
     * Happens just before rendering tabs group.
     */
    const TAB_GROUP_PRE_RENDER = 'ezplatform.tab.group.pre_render';

    /**
     * Happens just before rendering tab.
     */
    const TAB_PRE_RENDER = 'ezplatform.tab.pre_render';

    /**
     * Is dispatched on tabs extending AbstractEventDispatchingTab.
     *
     * Allows to manipulate template path and parameters before rendering by Twig.
     */
    const TAB_RENDER = 'ezplatform.tab.render';
}
