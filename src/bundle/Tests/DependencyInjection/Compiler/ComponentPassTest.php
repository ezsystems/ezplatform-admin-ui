<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Tests\DependencyInjection\Compiler;

use EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Compiler\ComponentPass;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use EzSystems\EzPlatformAdminUi\Component\Registry;
use Symfony\Component\DependencyInjection\Reference;
use EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException;

class ComponentPassTest extends AbstractCompilerPassTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->setDefinition(Registry::class, new Definition());
    }

    /**
     * @param ContainerBuilder $container
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ComponentPass());
    }

    public function testProcess()
    {
        $taggedServiceId = 'collected_service';
        $collectedService = new Definition();
        $collectedService->addTag(ComponentPass::TAG_NAME, ['group' => 'someGroup']);
        $this->setDefinition($taggedServiceId, $collectedService);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            $taggedServiceId,
            ComponentPass::TAG_NAME,
            ['group' => 'someGroup']
        );

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            Registry::class,
            'addComponent',
            ['someGroup', $taggedServiceId, new Reference($taggedServiceId)]
        );
    }

    public function testProcessWithNoGroup()
    {
        $taggedServiceId = 'collected_service';

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('Argument \'%s\' is invalid: Tag %s must contain "group" argument.', $taggedServiceId, ComponentPass::TAG_NAME));

        $collectedService = new Definition();
        $collectedService->addTag(ComponentPass::TAG_NAME);
        $this->setDefinition($taggedServiceId, $collectedService);

        $this->compile();
    }
}
