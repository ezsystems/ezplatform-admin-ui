<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Config\Mapper\FieldType\RichText\CustomTag;

use EzSystems\EzPlatformAdminUi\UI\LabelMaker\LabelMaker;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Map RichText Custom Tag attribute of any type to proper UI config.
 */
class CommonAttributeMapper implements AttributeMapper
{
    /**
     * @var TranslatorInterface
     * @deprecated Deprecated since v1.2.0. Label generation is now covered by a LabelMaker.
     */
    protected $translator;

    /**
     * @var string
     * @deprecated Deprecated since v1.2.0. Label generation is now covered by a LabelMaker.
     */
    protected $translationDomain;
    /**
     * @var LabelMaker
     */
    private $richTextAttributeLabelMaker;

    public function __construct(
        TranslatorInterface $translator,
        string $translationDomain,
        LabelMaker $richTextAttributeLabelMaker = null
    ) {
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
        $this->richTextAttributeLabelMaker = $richTextAttributeLabelMaker;
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
            'label' => $this->getLabel($tagName, $attributeName),
            'type' => $customTagAttributeProperties['type'],
            'required' => $customTagAttributeProperties['required'],
            'defaultValue' => $customTagAttributeProperties['default_value'],
        ];
    }

    /**
     * @param string $tagName
     * @param string $attributeName
     * @return string
     */
    private function getLabel(string $tagName, string $attributeName): string
    {
        /** @deprecated v1.2.0 backward compatibility */
        if ($this->richTextAttributeLabelMaker === null) {
            return $this->translator->trans(
                sprintf(
                    'ezrichtext.custom_tags.%s.attributes.%s.label',
                    $tagName,
                    $attributeName
                ),
                [],
                $this->translationDomain
            );
        }

        return $this->richTextAttributeLabelMaker->getLabel('label', [$tagName, $attributeName]);
    }
}
