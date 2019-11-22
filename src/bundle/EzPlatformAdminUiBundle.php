<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle;

use EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Compiler\AdminThemePathPass;
use EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Compiler\ComponentPass;
use EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Compiler\SecurityLoginPass;
use EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Compiler\SystemInfoTabGroupPass;
use EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Compiler\TabPass;
use EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Compiler\UiConfigProviderPass;
use EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Compiler\ViewBuilderRegistryPass;
use EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Configuration\Parser;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EzPlatformAdminUiBundle extends Bundle
{
    public const ADMIN_GROUP_NAME = 'admin_group';

    /**
     * {@inheritdoc}
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\LogicException
     */
    public function build(ContainerBuilder $container)
    {
        /** @var \eZ\Bundle\EzPublishCoreBundle\DependencyInjection\EzPublishCoreExtension $core */
        $core = $container->getExtension('ezpublish');

        $configParsers = $this->getConfigParsers();
        array_walk($configParsers, [$core, 'addConfigParser']);

        $core->addDefaultSettings(__DIR__ . '/Resources/config', ['ezplatform_default_settings.yml']);

        $this->addCompilerPasses($container);
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    private function addCompilerPasses(ContainerBuilder $container)
    {
        $container->addCompilerPass(new TabPass());
        $container->addCompilerPass(new UiConfigProviderPass());
        $container->addCompilerPass(new SystemInfoTabGroupPass());
        $container->addCompilerPass(new ComponentPass());
        $container->addCompilerPass(new SecurityLoginPass());
        $container->addCompilerPass(new ViewBuilderRegistryPass());
        $container->addCompilerPass(new AdminThemePathPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 1);
    }

    /**
     * @return \eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ParserInterface[]
     */
    private function getConfigParsers(): array
    {
        return [
            new Parser\LocationIds(),
            new Parser\Module\Subitems(),
            new Parser\Module\UniversalDiscoveryWidget(),
            new Parser\Module\ContentTree(),
            new Parser\Pagination(),
            new Parser\Security(),
            new Parser\UserIdentifier(),
            new Parser\UserGroupIdentifier(),
            new Parser\SubtreeOperations(),
            new Parser\Notifications(),
            new Parser\ContentTranslateView(),
            new Parser\AdminUiForms(),
            new Parser\ContentType(),
            new Parser\SubtreePath(),
        ];
    }
}
