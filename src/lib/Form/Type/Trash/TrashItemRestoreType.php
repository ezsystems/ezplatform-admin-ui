<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\Type\Trash;

use EzSystems\EzPlatformAdminUi\Form\Data\Trash\TrashItemRestoreData;
use EzSystems\EzPlatformAdminUi\Form\Type\UniversalDiscoveryWidget\UniversalDiscoveryWidgetType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrashItemRestoreType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'trash_items',
                TrashItemChoiceType::class,
                [
                    'multiple' => true,
                    'expanded' => true,
                    'choice_label' => false,
                    'label' => false,
                ]
            )
            ->add(
                'location',
                UniversalDiscoveryWidgetType::class,
                [
                    'multiple' => false,
                    'label' => /** @Desc("Restore under new parent") */
                        'trash_item_restore_form.restore_under_new_parent',
                    'attr' => $options['attr'],
                ]
            )
            ->add(
                'restore',
                SubmitType::class,
                [
                    'label' => /** @Desc("Restore selected") */
                        'trash_item_restore_form.restore',
                ]
            );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TrashItemRestoreData::class,
            'translation_domain' => 'forms',
        ]);
    }
}
