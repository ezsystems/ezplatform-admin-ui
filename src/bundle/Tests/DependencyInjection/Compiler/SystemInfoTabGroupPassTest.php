<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Tests\DependencyInjection\Compiler;

use EzSystems\EzPlatformAdminUi\Tab\TabGroup;
use EzSystems\EzPlatformAdminUi\Tab\TabRegistry;
use EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Compiler\SystemInfoTabGroupPass;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class SystemInfoTabGroupPassTest extends AbstractCompilerPassTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->setDefinition(TabRegistry::class, new Definition());
    }

    /**
     * @param ContainerBuilder $container
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new SystemInfoTabGroupPass());
    }

    public function testProcess()
    {
        $tabGroupDefinition = new Definition(TabGroup::class, ['systeminfo']);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            TabRegistry::class,
            'addTabGroup',
            [$tabGroupDefinition]
        );
    }
}
