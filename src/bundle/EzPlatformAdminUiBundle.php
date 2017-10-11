<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\EzPublishCoreExtension;
use EzSystems\EzPlatformAdminUi\SiteAccess\AdminFilter;
use EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Compiler\MenuPass;
use EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Compiler\RepositoryFormsViewPass;
use EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Compiler\SystemInfoTabGroupPass;
use EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Compiler\TabPass;
use EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Compiler\UiConfigProviderPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EzPlatformAdminUiBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        /** @var EzPublishCoreExtension $core */
        $core = $container->getExtension('ezpublish');
        $core->addSiteAccessConfigurationFilter(
            new AdminFilter()
        );

        $this->addCompilerPasses($container);
    }

    /**
     * @param ContainerBuilder $container
     */
    private function addCompilerPasses(ContainerBuilder $container)
    {
        $container->addCompilerPass(new TabPass());
        $container->addCompilerPass(new MenuPass());
        $container->addCompilerPass(new RepositoryFormsViewPass());
        $container->addCompilerPass(new UiConfigProviderPass());
        $container->addCompilerPass(new SystemInfoTabGroupPass());
    }
}
