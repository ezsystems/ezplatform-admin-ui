<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\REST\Value;

use EzSystems\EzPlatformRest\Value as RestValue;

class OperationResponse extends RestValue
{
    /** @var int */
    public $statusCode;

    /** @var array */
    public $headers;

    /** @var string|null */
    public $content;

    /**
     * @param int $statusCode
     * @param array $headers
     * @param string|null $content
     */
    public function __construct(int $statusCode, array $headers, ?string $content)
    {
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        $this->content = $content;
    }
}
