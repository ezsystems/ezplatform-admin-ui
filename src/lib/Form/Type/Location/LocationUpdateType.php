<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\Location;

use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationUpdateData;
use EzSystems\EzPlatformAdminUi\Form\Type\Content\LocationType;
use EzSystems\EzPlatformAdminUi\Form\Type\ContentType\SortFieldChoiceType;
use EzSystems\EzPlatformAdminUi\Form\Type\ContentType\SortOrderChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocationUpdateType extends AbstractType
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
                'sort_field',
                SortFieldChoiceType::class,
                ['label' => /** @Desc("Sort field") */ 'location_update_form.sort_field']
            )
            ->add(
                'sort_order',
                SortOrderChoiceType::class,
                ['label' => /** @Desc("Sort order") */ 'location_update_form.sort_order']
            )
            ->add(
                'update',
                SubmitType::class,
                ['label' => /** @Desc("Update") */ 'location_update_form.update']
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => LocationUpdateData::class,
            'translation_domain' => 'content_type',
        ]);
    }
}
