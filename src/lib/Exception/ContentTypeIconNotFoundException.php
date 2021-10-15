<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Exception;

use Exception;
use RuntimeException;

class ContentTypeIconNotFoundException extends RuntimeException
{
    public function __construct($contentType, $code = 0, Exception $previous = null)
    {
        parent::__construct("No icon found for '$contentType' Content Type", $code, $previous);
    }
}

class_alias(ContentTypeIconNotFoundException::class, 'EzSystems\EzPlatformAdminUi\Exception\ContentTypeIconNotFoundException');
