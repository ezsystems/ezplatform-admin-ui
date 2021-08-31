<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Tests\Templating\Twig;

use eZ\Publish\Core\MVC\Symfony\Templating\Tests\Twig\Extension\FileSystemTwigIntegrationTestCase;
use eZ\Publish\Core\MVC\Symfony\Templating\Twig\FieldBlockRenderer;
use eZ\Publish\Core\MVC\Symfony\Templating\Twig\ResourceProviderInterface;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionData;
use EzSystems\EzPlatformAdminUiBundle\Templating\Twig\FieldEditRenderingExtension;
use Twig\Environment;

class FieldEditRenderingExtensionTest extends FileSystemTwigIntegrationTestCase
{
    /**
     * @return \Twig\Extension\ExtensionInterface[]
     */
    public function getExtensions(): array
    {
        $resourceProvider = $this->createMock(ResourceProviderInterface::class);
        $resourceProvider->method('getFieldDefinitionEditResources')->willReturn([
            [
                'template' => $this->getTemplatePath('fields_override1.html.twig'),
                'priority' => 10,
            ],
            [
                'template' => $this->getTemplatePath('fields_default.html.twig'),
                'priority' => 0,
            ],
            [
                'template' => $this->getTemplatePath('fields_override2.html.twig'),
                'priority' => 20,
            ],
        ]);

        $fieldBlockRenderer = new FieldBlockRenderer(
            $this->createMock(Environment::class),
            $resourceProvider,
            $this->getTemplatePath('base.html.twig')
        );

        return [new FieldEditRenderingExtension($fieldBlockRenderer)];
    }

    public function getFixturesDir(): string
    {
        return __DIR__ . '/_fixtures/field_edit_rendering_functions/';
    }

    public function getFieldDefinitionData($typeIdentifier, $id = null, $settings = []): FieldDefinitionData
    {
        return new FieldDefinitionData([
            'fieldDefinition' => new FieldDefinition([
                'id' => $id,
                'fieldSettings' => $settings,
                'fieldTypeIdentifier' => $typeIdentifier,
            ]),
        ]);
    }

    /**
     * @dataProvider getLegacyTests
     * @group legacy
     *
     * @param string $file
     * @param string $message
     * @param string $condition
     * @param array $templates
     * @param string $exception
     * @param array $outputs
     * @param string $deprecation
     */
    public function testLegacyIntegration(
        $file,
        $message,
        $condition,
        $templates,
        $exception,
        $outputs,
        $deprecation = ''
    ): void {
        // disable Twig legacy integration test to avoid producing risky warning
        self::markTestSkipped('This package does not contain Twig legacy integration test cases');
    }

    private function getTemplatePath(string $tpl): string
    {
        return 'templates/' . $tpl;
    }
}
