<?php

namespace EzPlatformAdminUi\Form\Type\Location;

use EzPlatformAdminUi\Form\Data\Location\LocationCopyData;
use EzPlatformAdminUi\Form\Type\Content\LocationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocationCopyType extends AbstractType
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
                'new_parent_location',
                LocationType::class,
                ['label' => false]
            )
            ->add(
                'copy',
                SubmitType::class,
                ['label' => /** @Desc("Copy") */ 'location_copy.copy', 'attr' => ['hidden' => true]]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => LocationCopyData::class,
            'translation_domain' => 'forms',
        ]);
    }
}
