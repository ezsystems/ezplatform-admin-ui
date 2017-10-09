<?php

namespace EzPlatformAdminUiBundle;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\EzPublishCoreExtension;
use EzPlatformAdminUi\SiteAccess\AdminFilter;
use EzPlatformAdminUiBundle\DependencyInjection\Compiler\MenuPass;
use EzPlatformAdminUiBundle\DependencyInjection\Compiler\RepositoryFormsViewPass;
use EzPlatformAdminUiBundle\DependencyInjection\Compiler\SystemInfoTabGroupPass;
use EzPlatformAdminUiBundle\DependencyInjection\Compiler\TabPass;
use EzPlatformAdminUiBundle\DependencyInjection\Compiler\UiConfigProviderPass;
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
