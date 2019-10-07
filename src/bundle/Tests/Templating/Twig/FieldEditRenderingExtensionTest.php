<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace EzSystems\EzPlatformAdminUiBundle\Tests\Templating\Twig;

use eZ\Publish\Core\MVC\Symfony\Templating\Tests\Twig\Extension\FileSystemTwigIntegrationTestCase;
use eZ\Publish\Core\MVC\Symfony\Templating\Twig\FieldBlockRenderer;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\FieldDefinitionData;
use EzSystems\EzPlatformAdminUiBundle\Templating\Twig\FieldEditRenderingExtension;

class FieldEditRenderingExtensionTest extends FileSystemTwigIntegrationTestCase
{
    public function getExtensions()
    {
        $fieldBlockRenderer = new FieldBlockRenderer();
        $fieldBlockRenderer->setBaseTemplate($this->getTemplatePath('base.html.twig'));
        $fieldBlockRenderer->setFieldDefinitionEditResources([
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

        return [new FieldEditRenderingExtension($fieldBlockRenderer)];
    }

    public function getFixturesDir()
    {
        return __DIR__ . '/_fixtures/field_edit_rendering_functions/';
    }

    public function getFieldDefinitionData($typeIdentifier, $id = null, $settings = [])
    {
        return new FieldDefinitionData([
            'fieldDefinition' => new FieldDefinition([
                'id' => $id,
                'fieldSettings' => $settings,
                'fieldTypeIdentifier' => $typeIdentifier,
            ]),
        ]);
    }

    private function getTemplatePath($tpl)
    {
        return 'templates/' . $tpl;
    }
}
