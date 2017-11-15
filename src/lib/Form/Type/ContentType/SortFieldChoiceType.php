<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\ContentType;

use eZ\Publish\API\Repository\Values\Content\Location;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Translation\Exception\InvalidArgumentException;

/**
 * Form type for sort field selection.
 */
class SortFieldChoiceType extends AbstractType
{
    /** @var TranslatorInterface */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => $this->getSortFieldChoices(),
            'choices_as_values' => true,
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
     * @throws InvalidArgumentException
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
            $this->translator->trans(/** @Desc("Modification date") */ 'content_type.sort_field.modified', [], 'content_type') => location::SORT_FIELD_MODIFIED,
            $this->translator->trans(/** @Desc("Publication date") */ 'content_type.sort_field.published', [], 'content_type') => Location::SORT_FIELD_PUBLISHED,
        ];
    }
}
