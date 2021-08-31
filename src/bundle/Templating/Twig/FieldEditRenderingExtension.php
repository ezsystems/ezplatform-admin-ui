<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Templating\Twig;

use eZ\Publish\Core\MVC\Symfony\Templating\Exception\MissingFieldBlockException;
use eZ\Publish\Core\MVC\Symfony\Templating\FieldBlockRendererInterface;
use EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionData;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FieldEditRenderingExtension extends AbstractExtension
{
    /** @var \eZ\Publish\Core\MVC\Symfony\Templating\FieldBlockRendererInterface|\eZ\Publish\Core\MVC\Symfony\Templating\Twig\FieldBlockRenderer */
    private $fieldBlockRenderer;

    public function __construct(FieldBlockRendererInterface $fieldBlockRenderer)
    {
        $this->fieldBlockRenderer = $fieldBlockRenderer;
    }

    public function getName(): string
    {
        return 'ezplatform.content_forms.field_edit_rendering';
    }

    /**
     * @return \Twig\TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'ez_render_field_definition_edit',
                function (Environment $twig, FieldDefinitionData $fieldDefinitionData, array $params = []) {
                    $this->fieldBlockRenderer->setTwig($twig);

                    return $this->renderFieldDefinitionEdit($fieldDefinitionData, $params);
                },
                ['is_safe' => ['html'], 'needs_environment' => true]
            ),
        ];
    }

    public function renderFieldDefinitionEdit(FieldDefinitionData $fieldDefinitionData, array $params = []): string
    {
        $params += ['data' => $fieldDefinitionData];
        try {
            return $this->fieldBlockRenderer->renderFieldDefinitionEdit($fieldDefinitionData->fieldDefinition, $params);
        } catch (MissingFieldBlockException $e) {
            // Silently fail on purpose.
            // If there is no template block for current field definition, there might not be anything specific to add.
            return '';
        }
    }
}
