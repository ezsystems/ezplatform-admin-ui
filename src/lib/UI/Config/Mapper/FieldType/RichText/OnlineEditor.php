<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Config\Mapper\FieldType\RichText;

use Symfony\Component\Translation\TranslatorInterface;

/**
 * Online Editor configuration mapper.
 */
class OnlineEditor implements OnlineEditorConfigMapper
{
    /** @var \Symfony\Component\Translation\TranslatorInterface */
    private $translator;

    /** @var string */
    private $translationDomain;

    /**
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     * @param string $translationDomain
     */
    public function __construct(TranslatorInterface $translator, string $translationDomain)
    {
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
    }

    /**
     * {@inheritdoc}
     */
    public function mapCssClassesConfiguration(array $semanticConfiguration): array
    {
        $configuration = [];
        foreach ($semanticConfiguration as $elementName => $elementConfiguration) {
            $label = $this->translator->trans(
                /** @Ignore */
                'ezrichtext.classes.class.label',
                [],
                $this->translationDomain
            );
            $configuration[$elementName] = [
                'choices' => $elementConfiguration['choices'],
                'required' => $elementConfiguration['required'],
                'defaultValue' => $elementConfiguration['default_value'] ?? null,
                'multiple' => $elementConfiguration['multiple'],
                'label' => $label,
            ];
        }

        return $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function mapDataAttributesConfiguration(array $semanticConfiguration): array
    {
        $configuration = [];
        foreach ($semanticConfiguration as $elementName => $elementAttributes) {
            foreach ($elementAttributes as $attributeName => $attributeConfiguration) {
                $type = $attributeConfiguration['type'];
                $config = [
                    'type' => $type,
                    'required' => $attributeConfiguration['required'],
                    'defaultValue' => $attributeConfiguration['default_value'] ?? null,
                ];
                if ($type === 'choice') {
                    $config['choices'] = $attributeConfiguration['choices'];
                    $config['multiple'] = $attributeConfiguration['multiple'];
                }

                $config['label'] = $this->translator->trans(
                    /** @Ignore */
                    "ezrichtext.attributes.{$elementName}.{$attributeName}.label",
                    [],
                    $this->translationDomain
                );

                $configuration[$elementName][$attributeName] = $config;
            }
        }

        return $configuration;
    }
}
