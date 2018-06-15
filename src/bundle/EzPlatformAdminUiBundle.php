<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle;

use EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Compiler\ComponentPass;
use EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Compiler\SecurityLoginPass;
use EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Compiler\SystemInfoTabGroupPass;
use EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Compiler\TabPass;
use EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Compiler\UiConfigProviderPass;
use EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Compiler\ViewBuilderRegistryPass;
use EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Configuration\Parser\ContentTranslateView;
use EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Configuration\Parser\LocationIds;
use EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Configuration\Parser\Module;
use EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Configuration\Parser\Notifications;
use EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Configuration\Parser\Pagination;
use EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Configuration\Parser\Security;
use EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Configuration\Parser\SubtreeOperations;
use EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Configuration\Parser\UserGroupIdentifier;
use EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Configuration\Parser\UserIdentifier;
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
    }

    /**
     * @return \eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ParserInterface[]
     */
    private function getConfigParsers(): array
    {
        return [
            new LocationIds(),
            new Module\Subitems(),
            new Module\UniversalDiscoveryWidget(),
            new Pagination(),
            new Security(),
            new UserIdentifier(),
            new UserGroupIdentifier(),
            new SubtreeOperations(),
            new Notifications(),
            new ContentTranslateView(),
        ];
    }
}
