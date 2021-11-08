<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\AdminUi\Exception;

use RuntimeException;

class MissingLimitationBlockException extends RuntimeException
{
}

class_alias(MissingLimitationBlockException::class, 'EzSystems\EzPlatformAdminUi\Exception\MissingLimitationBlockException');
