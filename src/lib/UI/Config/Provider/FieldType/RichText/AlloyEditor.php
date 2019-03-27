<?php

declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Config\Provider\FieldType\RichText;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use EzSystems\EzPlatformAdminUi\UI\Config\ProviderInterface;

class AlloyEditor implements ProviderInterface
{
    /** @var ConfigResolverInterface */
    private $configResolver;

    /**
     * @param ConfigResolverInterface $configResolver
     */
    public function __construct(ConfigResolverInterface $configResolver) {
        $this->configResolver = $configResolver;
    }

    /**
     * @return array AlloyEditor config
     */
    public function getConfig(): array
    {
        return [
            'customPlugins' => $this->getCustomPlugins()
        ];
    }

    /**
     * @return array Custom plugins
     */
    protected function getCustomPlugins(): array
    {
        if ($this->configResolver->hasParameter('alloy_editor')) {
            $param = $this->configResolver->getParameter('alloy_editor');
            if (isset($param['custom_plugins'])) {
                return $param['custom_plugins'];
            }
        }

        return [];
    }
}
