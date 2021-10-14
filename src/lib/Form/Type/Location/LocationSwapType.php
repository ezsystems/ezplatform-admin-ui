<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Form\Type\Location;

use Ibexa\AdminUi\Form\Data\Location\LocationSwapData;
use Ibexa\AdminUi\Form\Type\Content\LocationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocationSwapType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'current_location',
                LocationType::class,
                ['label' => false]
            )
            ->add(
                'new_location',
                LocationType::class,
                ['label' => false]
            )
            ->add(
                'swap',
                SubmitType::class,
                ['label' => /** @Desc("Select Content item") */ 'swap_location_form.swap']
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => LocationSwapData::class,
            'translation_domain' => 'forms',
        ]);
    }
}

class_alias(LocationSwapType::class, 'EzSystems\EzPlatformAdminUi\Form\Type\Location\LocationSwapType');
