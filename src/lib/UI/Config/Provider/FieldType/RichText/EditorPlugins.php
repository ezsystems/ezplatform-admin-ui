<?php

declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Config\Provider\FieldType\RichText;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use EzSystems\EzPlatformAdminUi\UI\Config\ProviderInterface;

class EditorPlugins implements ProviderInterface
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
     * @return array RichText Editor Plugins
     */
    public function getConfig(): array
    {
        if ($this->configResolver->hasParameter('editor_plugins')) {
            return $this->configResolver->getParameter('editor_plugins');
        }

        return [];
    }
}
