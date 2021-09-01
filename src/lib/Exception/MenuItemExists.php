<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\AdminUi\Exception;

use Exception;

class MenuItemExists extends Exception
{
}

class_alias(MenuItemExists::class, 'EzSystems\EzPlatformAdminUi\Exception\MenuItemExists');
