<?php
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Config\Provider;

use EzSystems\EzPlatformAdminUi\UI\Config\ProviderInterface;

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
     * {@inheritdoc}
     *
     * @return mixed
     */
    public function getConfig()
    {
        return $this->config;
    }
}
