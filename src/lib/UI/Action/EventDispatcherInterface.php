<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Action;

interface EventDispatcherInterface
{
    public const EVENT_NAME_PREFIX = 'ezplatform.admin_ui.action';

    /**
     * @param \EzSystems\EzPlatformAdminUi\UI\Action\UiActionEventInterface $event
     */
    public function dispatch(UiActionEventInterface $event): void;
}
