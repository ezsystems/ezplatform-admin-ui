<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Config\Mapper\FieldType\RichText;

use EzSystems\EzPlatformAdminUi\UI\Config\Mapper\FieldType\RichText\CustomTag\AttributeMapper;
use EzSystems\EzPlatformAdminUi\UI\LabelMaker\LabelMaker;
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

    /**
     * @var TranslatorInterface
     *
     * @deprecated Deprecated since v1.2.0. Label generation is now covered by a LabelMaker.
     */
    private $translator;

    /** @var Packages */
    private $packages;

    /** @var AttributeMapper[] */
    private $customTagAttributeMappers;

    /** @var AttributeMapper[] */
    private $supportedTagAttributeMappersCache;

    /**
     * @var string
     *
     * @deprecated Deprecated since v1.2.0. Label generation is now covered by a LabelMaker.
     */
    private $translationDomain;

    /** @var LabelMaker */
    private $labelMaker;

    public function __construct(
        array $customTagsConfiguration,
        TranslatorInterface $translator,
        string $translationDomain,
        Packages $packages,
        Traversable $customTagAttributeMappers,
        LabelMaker $labelMaker
    ) {
        $this->customTagsConfiguration = $customTagsConfiguration;
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
        $this->packages = $packages;
        $this->customTagAttributeMappers = $customTagAttributeMappers;
        $this->supportedTagAttributeMappersCache = [];
        $this->labelMaker = $labelMaker;
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

            if (!empty($customTagConfiguration['icon'])) {
                $config[$tagName]['icon'] = $this->packages->getUrl(
                    $customTagConfiguration['icon']
                );
            }

            $config[$tagName]['label'] = $this->labelMaker->getLabel('label', $tagName);
            $config[$tagName]['description'] = $this->labelMaker->getLabel('description', $tagName, false);

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

        return $config;
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
}
