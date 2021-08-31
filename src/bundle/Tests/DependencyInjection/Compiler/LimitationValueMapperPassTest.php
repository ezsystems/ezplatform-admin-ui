<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Tests\DependencyInjection\Compiler;

use EzSystems\EzPlatformAdminUi\Limitation\LimitationValueMapperInterface;
use EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Compiler\LimitationValueMapperPass;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class LimitationValueMapperPassTest extends AbstractCompilerPassTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setDefinition(LimitationValueMapperPass::LIMITATION_VALUE_MAPPER_REGISTRY, new Definition());
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new LimitationValueMapperPass());
    }

    public function testRegisterMappers(): void
    {
        $limitationValueMapperServiceId = 'limitationvalue_mapper';

        $def = new Definition();
        $def->addTag(LimitationValueMapperPass::LIMITATION_VALUE_MAPPER_TAG, [
            'limitationType' => 'limitation',
        ]);
        $def->setClass(\get_class($this->createMock(LimitationValueMapperInterface::class)));
        $this->setDefinition($limitationValueMapperServiceId, $def);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            LimitationValueMapperPass::LIMITATION_VALUE_MAPPER_REGISTRY,
            'addMapper',
            [new Reference($limitationValueMapperServiceId), 'limitation']
        );
    }
}
