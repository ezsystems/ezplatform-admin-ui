<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\REST\Value;

use EzSystems\EzPlatformRest\Value as RestValue;

class Operation extends RestValue
{
    /** @var string */
    public $uri;

    /** @var string */
    public $method;

    /** @var array */
    public $parameters;

    /** @var array */
    public $headers;

    /** @var string */
    public $content;

    /**
     * @param string $uri
     * @param string $method
     * @param array $parameters
     * @param array $headers
     * @param string $content
     */
    public function __construct(
        string $uri,
        string $method,
        array $parameters,
        array $headers,
        string $content
    ) {
        $this->uri = $uri;
        $this->method = $method;
        $this->parameters = $parameters;
        $this->headers = $headers;
        $this->content = $content;
    }
}
