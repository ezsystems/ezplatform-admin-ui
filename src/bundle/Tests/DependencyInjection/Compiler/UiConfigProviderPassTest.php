<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Compiler;

use EzSystems\EzPlatformAdminUi\UI\Config\Aggregator;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class UiConfigProviderPassTest extends AbstractCompilerPassTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->setDefinition(Aggregator::class, new Definition());
    }

    /**
     * @param ContainerBuilder $container
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new UiConfigProviderPass());
    }

    public function testProcess()
    {
        $taggedServiceId = 'collected_service';
        $collectedService = new Definition();
        $collectedService->addTag(UiConfigProviderPass::TAG_CONFIG_PROVIDER, ['key' => 'someKey']);
        $this->setDefinition($taggedServiceId, $collectedService);

        $taggedServiceWithoutKeyId = 'collected_service_without_key';
        $collectedServiceWithoutKey = new Definition();
        $collectedServiceWithoutKey->addTag(UiConfigProviderPass::TAG_CONFIG_PROVIDER);
        $this->setDefinition($taggedServiceWithoutKeyId, $collectedServiceWithoutKey);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            $taggedServiceId,
            UiConfigProviderPass::TAG_CONFIG_PROVIDER,
            ['key' => 'someKey']
        );

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            $taggedServiceWithoutKeyId,
            UiConfigProviderPass::TAG_CONFIG_PROVIDER
        );

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            Aggregator::class,
            'addProvider',
            ['someKey', new Reference($taggedServiceId)]
        );

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            Aggregator::class,
            'addProvider',
            [$taggedServiceWithoutKeyId, new Reference($taggedServiceWithoutKeyId)]
        );
    }
}
