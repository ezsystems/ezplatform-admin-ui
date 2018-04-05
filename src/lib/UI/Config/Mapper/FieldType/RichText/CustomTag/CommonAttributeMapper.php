<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Config\Mapper\FieldType\RichText\CustomTag;

use Symfony\Component\Translation\TranslatorInterface;

/**
 * Map RichText Custom Tag attribute of any type to proper UI config.
 */
class CommonAttributeMapper implements AttributeMapper
{
    /** @var TranslatorInterface */
    protected $translator;

    /** @var string */
    protected $translationDomain;

    public function __construct(TranslatorInterface $translator, string $translationDomain)
    {
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
    }

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
            'label' => $this->translator->trans(
                sprintf(
                    'ezrichtext.custom_tags.%s.attributes.%s.label',
                    $tagName,
                    $attributeName
                ),
                [],
                $this->translationDomain
            ),
            'type' => $customTagAttributeProperties['type'],
            'required' => $customTagAttributeProperties['required'],
            'defaultValue' => $customTagAttributeProperties['default_value'],
        ];
    }
}
