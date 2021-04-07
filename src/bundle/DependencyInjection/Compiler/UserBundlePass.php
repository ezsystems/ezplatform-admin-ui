<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Compiler;

use EzSystems\EzPlatformAdminUi\Notification\TranslatableNotificationHandler;
use EzSystems\EzPlatformUser\ExceptionHandler\ActionResultHandler;
use EzSystems\EzPlatformUser\ExceptionHandler\NullActionResultHandler;
use EzSystems\EzPlatformUser\Form\BaseSubmitHandler;
use EzSystems\EzPlatformUser\Form\SubmitHandler;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class UserBundlePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $this->setActionResultAlias($container);
        $this->setSubmitHandlerAlias($container);
    }

    private function setActionResultAlias(ContainerBuilder $container): void
    {
        if (!$container->hasAlias(ActionResultHandler::class)) {
            return;
        }

        $actionResultAlias = $container->getAlias(ActionResultHandler::class);
        if ($actionResultAlias === NullActionResultHandler::class) {
            $container->setAlias(ActionResultHandler::class, TranslatableNotificationHandler::class);
        }
    }

    private function setSubmitHandlerAlias(ContainerBuilder $container): void
    {
        if (!$container->hasAlias(SubmitHandler::class)) {
            return;
        }

        $actionResultAlias = $container->getAlias(SubmitHandler::class);
        if ($actionResultAlias === BaseSubmitHandler::class) {
            $container->setAlias(SubmitHandler::class, BaseSubmitHandler::class);
        }
    }
}
