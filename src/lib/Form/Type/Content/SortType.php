<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\Content;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('field', ChoiceType::class, [
                'choices' => $options['sort_fields'],
                'attr' => ['hidden' => true],
                'required' => true,
                'placeholder' => false,
                'empty_data' => $options['default']['field'],
            ])
            ->add('direction', IntegerType::class, [
                'attr' => ['hidden' => true],
                'required' => false,
                'empty_data' => $options['default']['direction'],
            ]);
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefined('sort_fields');
        $optionsResolver->setDefined('default');
        $optionsResolver->setAllowedTypes('sort_fields', 'array');
        $optionsResolver->setAllowedTypes('default', 'array');
    }
}
