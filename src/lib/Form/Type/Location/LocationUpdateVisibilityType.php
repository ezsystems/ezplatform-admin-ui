<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Form\Type\Location;

use Ibexa\AdminUi\Form\Data\Location\LocationUpdateVisibilityData;
use Ibexa\AdminUi\Form\Type\Content\LocationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocationUpdateVisibilityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'location',
                LocationType::class,
                ['label' => false]
            )
            ->add(
                'hidden',
                CheckboxType::class,
                ['label' => false, 'required' => false, 'attr' => ['hidden' => true]]
            )
            ->add(
                'set',
                SubmitType::class,
                ['label' => false, 'attr' => ['hidden' => true]]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => LocationUpdateVisibilityData::class,
            'translation_domain' => 'forms',
        ]);
    }
}

class_alias(LocationUpdateVisibilityType::class, 'EzSystems\EzPlatformAdminUi\Form\Type\Location\LocationUpdateVisibilityType');
