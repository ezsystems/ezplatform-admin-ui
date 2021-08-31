<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\Type\Trash;

use EzSystems\EzPlatformAdminUi\Form\Data\Trash\TrashEmptyData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrashEmptyType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'empty_trash',
                CheckboxType::class,
                [
                    'required' => false,
                    'label' => false,
                ]
            )
            ->add(
                'empty',
                SubmitType::class,
                [
                    'label' => /** @Desc("Delete permanently") */
                        'trash_empty_form.empty',
                ]
            );
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TrashEmptyData::class,
            'translation_domain' => 'forms',
        ]);
    }
}
