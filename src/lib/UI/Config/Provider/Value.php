<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\UI\Config\Provider;

use Ibexa\Contracts\AdminUi\UI\Config\ProviderInterface;

/**
 * Simple value provider that passes on the value it is given in the constructor.
 * Can be used for container config.
 */
class Value implements ProviderInterface
{
    /** @var mixed */
    protected $config;

    /**
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->config = $value;
    }

    /**
     * @inheritdoc
     *
     * @return mixed
     */
    public function getConfig()
    {
        return $this->config;
    }
}

class_alias(Value::class, 'EzSystems\EzPlatformAdminUi\UI\Config\Provider\Value');
