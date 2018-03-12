<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\EzPublishCoreExtension;
use EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Compiler\ComponentPass;
use EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Compiler\SecurityLoginPass;
use EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Compiler\SystemInfoTabGroupPass;
use EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Compiler\TabPass;
use EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Compiler\UiConfigProviderPass;
use EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Configuration\Parser\LocationIds;
use EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Configuration\Parser\Module;
use EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Configuration\Parser\Pagination;
use EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Configuration\Parser\Security;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EzPlatformAdminUiBundle extends Bundle
{
    public const ADMIN_GROUP_NAME = 'admin_group';

    public function build(ContainerBuilder $container)
    {
        /** @var EzPublishCoreExtension $core */
        $core = $container->getExtension('ezpublish');
        $core->addConfigParser(new LocationIds());
        $core->addConfigParser(new Module\Subitems());
        $core->addConfigParser(new Module\UniversalDiscoveryWidget());
        $core->addConfigParser(new Pagination());
        $core->addConfigParser(new Security());
        $core->addDefaultSettings(__DIR__ . '/Resources/config', ['ezplatform_default_settings.yml']);

        $this->addCompilerPasses($container);
    }

    /**
     * @param ContainerBuilder $container
     */
    private function addCompilerPasses(ContainerBuilder $container)
    {
        $container->addCompilerPass(new TabPass());
        $container->addCompilerPass(new UiConfigProviderPass());
        $container->addCompilerPass(new SystemInfoTabGroupPass());
        $container->addCompilerPass(new ComponentPass());
        $container->addCompilerPass(new SecurityLoginPass());
    }
}
