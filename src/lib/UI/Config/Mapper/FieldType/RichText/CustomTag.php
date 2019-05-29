<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Config\Mapper\FieldType\RichText;

use EzSystems\EzPlatformAdminUi\UI\Config\Mapper\FieldType\RichText\CustomTag\AttributeMapper;
use RuntimeException;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Translation\TranslatorInterface;
use Traversable;

/**
 * RichText Custom Tag configuration mapper.
 */
class CustomTag
{
    /** @var array */
    private $customTagsConfiguration;

    /** @var TranslatorInterface */
    private $translator;

    /** @var Packages */
    private $packages;

    /** @var AttributeMapper[] */
    private $customTagAttributeMappers;

    /** @var AttributeMapper[] */
    private $supportedTagAttributeMappersCache;

    /** @var string */
    private $translationDomain;

    public function __construct(
        array $customTagsConfiguration,
        TranslatorInterface $translator,
        string $translationDomain,
        Packages $packages,
        Traversable $customTagAttributeMappers
    ) {
        $this->customTagsConfiguration = $customTagsConfiguration;
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
        $this->packages = $packages;
        $this->customTagAttributeMappers = $customTagAttributeMappers;
        $this->supportedTagAttributeMappersCache = [];
    }

    /**
     * Map Configuration for the given list of enabled Custom Tags.
     *
     * @param array $enabledCustomTags
     *
     * @return array Mapped configuration
     */
    public function mapConfig(array $enabledCustomTags)
    {
        $config = [];
        foreach ($enabledCustomTags as $tagName) {
            if (!isset($this->customTagsConfiguration[$tagName])) {
                throw new RuntimeException(
                    "RichText Custom Tag configuration for {$tagName} not found."
                );
            }

            $customTagConfiguration = $this->customTagsConfiguration[$tagName];

            $config[$tagName] = [
                'label' => "ezrichtext.custom_tags.{$tagName}.label",
                'description' => "ezrichtext.custom_tags.{$tagName}.description",
                'isInline' => $customTagConfiguration['is_inline'],
            ];

            if (!empty($customTagConfiguration['icon'])) {
                $config[$tagName]['icon'] = $this->packages->getUrl(
                    $customTagConfiguration['icon']
                );
            }

            foreach ($customTagConfiguration['attributes'] as $attributeName => $properties) {
                $typeMapper = $this->getAttributeTypeMapper(
                    $tagName,
                    $attributeName,
                    $properties['type']
                );
                $config[$tagName]['attributes'][$attributeName] = $typeMapper->mapConfig(
                    $tagName,
                    $attributeName,
                    $properties
                );
            }
        }

        return $this->translateLabels($config);
    }

    /**
     * Get first available Custom Tag Attribute Type mapper.
     *
     * @param string $tagName
     * @param string $attributeName
     * @param string $attributeType
     *
     * @return AttributeMapper
     */
    private function getAttributeTypeMapper(
        string $tagName,
        string $attributeName,
        string $attributeType
    ): AttributeMapper {
        if (isset($this->supportedTagAttributeMappersCache[$attributeType])) {
            return $this->supportedTagAttributeMappersCache[$attributeType];
        }

        foreach ($this->customTagAttributeMappers as $attributeMapper) {
            // get first supporting, order of these mappers is controlled by 'priority' DI tag attribute
            if ($attributeMapper->supports($attributeType)) {
                return $this->supportedTagAttributeMappersCache[$attributeType] = $attributeMapper;
            }
        }

        throw new RuntimeException(
            "RichText Custom Tag configuration: unsupported attribute type '{$attributeType}' of '{$attributeName}' attribute of '{$tagName}' Custom Tag"
        );
    }

    /**
     * Process Custom Tags config and translate labels for UI.
     *
     * @param array $config
     *
     * @return array processed Custom Tags config with translated labels
     */
    private function translateLabels(array $config): array
    {
        foreach ($config as $tagName => $tagConfig) {
            $config[$tagName]['label'] = $this->translator->trans(
                /** @Ignore */
                $tagConfig['label'],
                [],
                $this->translationDomain
            );
            $config[$tagName]['description'] = $this->translator->trans(
                /** @Ignore */
                $tagConfig['description'],
                [],
                $this->translationDomain
            );

            if (empty($tagConfig['attributes'])) {
                continue;
            }

            foreach ($tagConfig['attributes'] as $attributeName => $attributeConfig) {
                $config[$tagName]['attributes'][$attributeName]['label'] = $this->translator->trans(
                    /** @Ignore */
                    $attributeConfig['label'],
                    [],
                    $this->translationDomain
                );

                if (isset($config[$tagName]['attributes'][$attributeName]['choicesLabel'])) {
                    foreach ($config[$tagName]['attributes'][$attributeName]['choicesLabel'] as $choice => $label) {
                        $translatedLabel = $this->translator->trans(
                            /** @Ignore */
                            $label,
                            [],
                            $this->translationDomain
                        );
                        $hasTranslation = $translatedLabel !== $label;

                        $config[$tagName]['attributes'][$attributeName]['choicesLabel'][$choice] = $hasTranslation ? $translatedLabel : $choice;
                    }
                }
            }
        }

        return $config;
    }
}
