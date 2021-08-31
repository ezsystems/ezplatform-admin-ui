<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Event;

use eZ\Publish\SPI\Options\OptionsBag;

final class Options implements OptionsBag
{
    /** @var array */
    private $options;

    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    public function all(): array
    {
        return $this->options;
    }

    public function get(
        string $key,
        $default = null
    ) {
        return $this->has($key)
            ? $this->options[$key]
            : $default;
    }

    public function has(string $key): bool
    {
        return isset($this->options[$key]);
    }
}
