<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Tests\DependencyInjection\Compiler;

use EzSystems\EzPlatformAdminUi\Tab\TabRegistry;
use EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Compiler\TabPass;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class TabPassTest extends AbstractCompilerPassTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setDefinition(TabRegistry::class, new Definition());
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new TabPass());
    }

    public function testProcess()
    {
        $taggedServiceId = 'collected_service';
        $collectedService = new Definition();
        $collectedService->addTag(TabPass::TAG_TAB, ['group' => 'someGroup']);
        $this->setDefinition($taggedServiceId, $collectedService);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            $taggedServiceId,
            TabPass::TAG_TAB,
            ['group' => 'someGroup']
        );

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            TabRegistry::class,
            'addTab',
            [new Reference($taggedServiceId), 'someGroup']
        );
    }
}
