<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Config\Mapper\FieldType\RichText\CustomTag;

/**
 * Map RichText Custom Tag attribute of any type to proper UI config.
 */
class CommonAttributeMapper implements AttributeMapper
{
    /**
     * {@inheritdoc}
     */
    public function supports(string $attributeType): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function mapConfig(
        string $tagName,
        string $attributeName,
        array $customTagAttributeProperties
    ): array {
        return [
            'label' => "ezrichtext.custom_tags.{$tagName}.attributes.{$attributeName}.label",
            'type' => $customTagAttributeProperties['type'],
            'required' => $customTagAttributeProperties['required'],
            'defaultValue' => $customTagAttributeProperties['default_value'],
        ];
    }
}
