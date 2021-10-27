<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\AdminUi\Exception;

use Exception;

class MenuItemNotExists extends Exception
{
}

class_alias(MenuItemNotExists::class, 'EzSystems\EzPlatformAdminUi\Exception\MenuItemNotExists');
