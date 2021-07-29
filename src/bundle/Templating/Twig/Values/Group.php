<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Templating\Twig\Values;

/**
 * @internal
 *
 * @see \EzSystems\EzPlatformAdminUiBundle\Templating\Twig\GroupByExtension
 */
final class Group
{
    /** @var string|int|object */
    public $key;

    /** @var array<mixed> */
    public $entries;

    /**
     * @param string|int|object
     */
    public function __construct($key)
    {
        $this->key = $key;
        $this->entries = [];
    }
}
