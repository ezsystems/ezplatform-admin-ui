<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Form\Type\Location;

use Ibexa\AdminUi\Form\Data\Location\LocationAssignSubtreeData;
use Ibexa\AdminUi\Form\Type\Content\LocationType;
use Ibexa\AdminUi\Form\Type\Section\SectionChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocationAssignSectionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('section', SectionChoiceType::class, [
                'label' => false,
                'multiple' => false,
            ])
            ->add('location', LocationType::class)
            ->add('assign', SubmitType::class, [
                'label' => /** @Desc("Change Section") */ 'section_subtree_assign_form.assign',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => LocationAssignSubtreeData::class,
            'translation_domain' => 'forms',
        ]);
    }
}

class_alias(LocationAssignSectionType::class, 'EzSystems\EzPlatformAdminUi\Form\Type\Location\LocationAssignSectionType');
