<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\AdminUi\DependencyInjection\Compiler;

use Ibexa\AdminUi\Security\Authentication\RedirectToDashboardAuthenticationSuccessHandler;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SecurityLoginPass implements CompilerPassInterface
{
    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $successHandlerDef = $container->getDefinition('security.authentication.success_handler');
        $successHandlerDef->setClass(RedirectToDashboardAuthenticationSuccessHandler::class);
        $successHandlerDef->addArgument($container->getParameter('ezpublish.siteaccess.groups'));
        $successHandlerDef->addArgument('ezplatform.dashboard');
    }
}

class_alias(SecurityLoginPass::class, 'EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Compiler\SecurityLoginPass');
