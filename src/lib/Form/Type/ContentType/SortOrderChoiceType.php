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
 * Form type for sort order selection.
 */
class SortOrderChoiceType extends AbstractType
{
    /**
     * @var \Symfony\Contracts\Translation\TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => $this->getSortOrderChoices(),
            'translation_domain' => 'content_type',
        ]);
    }

    public function getParent(): ?string
    {
        return ChoiceType::class;
    }

    /**
     * Generate sort order options available to choose.
     *
     * @return array
     */
    private function getSortOrderChoices()
    {
        $choices = [];
        foreach ($this->getSortOrder() as $label => $value) {
            $choices[$label] = $value;
        }

        return $choices;
    }

    /**
     * Get available sort order values.
     *
     * @return array
     */
    private function getSortOrder()
    {
        return [
            $this->translator->trans(/** @Desc("Ascending") */ 'content_type.sort_field.ascending', [], 'content_type') => Location::SORT_ORDER_ASC,
            $this->translator->trans(/** @Desc("Descending") */ 'content_type.sort_field.descending', [], 'content_type') => Location::SORT_ORDER_DESC,
        ];
    }
}
