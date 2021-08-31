<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\ContentType;

use eZ\Publish\API\Repository\Values\Content\Location;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Form type for sort field selection.
 */
class SortFieldChoiceType extends AbstractType
{
    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => $this->getSortFieldChoices(),
            'translation_domain' => 'content_type',
        ]);
    }

    public function getParent(): ?string
    {
        return ChoiceType::class;
    }

    /**
     * Generate sort field options available to choose.
     *
     * @return array
     *
     * @throws \Symfony\Component\Translation\Exception\InvalidArgumentException
     */
    private function getSortFieldChoices(): array
    {
        $choices = [];
        foreach ($this->getSortField() as $label => $value) {
            $choices[$label] = $value;
        }

        return $choices;
    }

    /**
     * Returns available sort field values.
     *
     * @return array
     */
    private function getSortField(): array
    {
        return [
            $this->translator->trans(/** @Desc("Content name") */ 'content_type.sort_field.name', [], 'content_type') => Location::SORT_FIELD_NAME,
            $this->translator->trans(/** @Desc("Location priority") */ 'content_type.sort_field.priority', [], 'content_type') => Location::SORT_FIELD_PRIORITY,
            $this->translator->trans(/** @Desc("Modification date") */ 'content_type.sort_field.modified', [], 'content_type') => Location::SORT_FIELD_MODIFIED,
            $this->translator->trans(/** @Desc("Publication date") */ 'content_type.sort_field.published', [], 'content_type') => Location::SORT_FIELD_PUBLISHED,
            $this->translator->trans(/** @Desc("Location path") */ 'content_type.sort_field.location_path', [], 'content_type') => Location::SORT_FIELD_PATH,
            $this->translator->trans(/** @Desc("Section identifier") */ 'content_type.sort_field.section_identifier', [], 'content_type') => Location::SORT_FIELD_SECTION,
            $this->translator->trans(/** @Desc("Location depth") */ 'content_type.sort_field.depth', [], 'content_type') => Location::SORT_FIELD_DEPTH,
            $this->translator->trans(/** @Desc("Location ID") */ 'content_type.sort_field.location_id', [], 'content_type') => Location::SORT_FIELD_NODE_ID,
            $this->translator->trans(/** @Desc("Content ID") */ 'content_type.sort_field.content_id', [], 'content_type') => Location::SORT_FIELD_CONTENTOBJECT_ID,
        ];
    }
}
