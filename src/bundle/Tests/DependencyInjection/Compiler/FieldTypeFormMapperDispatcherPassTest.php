<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Tests\DependencyInjection\Compiler;

use EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Compiler\FieldTypeFormMapperDispatcherPass;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class FieldTypeFormMapperDispatcherPassTest extends AbstractCompilerPassTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setDefinition(FieldTypeFormMapperDispatcherPass::FIELD_TYPE_FORM_MAPPER_DISPATCHER, new Definition());
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new FieldTypeFormMapperDispatcherPass());
    }

    /**
     * @dataProvider tagsProvider
     */
    public function testRegisterMappers(string $tag): void
    {
        $fieldTypeIdentifier = 'field_type_identifier';
        $serviceId = 'service_id';
        $def = new Definition();
        $def->addTag($tag, ['fieldType' => $fieldTypeIdentifier]);
        $this->setDefinition($serviceId, $def);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            FieldTypeFormMapperDispatcherPass::FIELD_TYPE_FORM_MAPPER_DISPATCHER,
            'addMapper',
            [new Reference($serviceId), $fieldTypeIdentifier]
        );
    }

    public function tagsProvider(): array
    {
        return [
            [FieldTypeFormMapperDispatcherPass::DEPRECATED_FIELD_TYPE_FORM_MAPPER_DEFINITION_SERVICE_TAG],
            [FieldTypeFormMapperDispatcherPass::FIELD_TYPE_FORM_MAPPER_DEFINITION_SERVICE_TAG],
        ];
    }
}
