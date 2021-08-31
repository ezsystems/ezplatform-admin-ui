<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\ObjectState;

use EzSystems\EzPlatformAdminUi\Form\Data\ObjectState\ObjectStateUpdateData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ObjectStateUpdateType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('identifier', TextType::class, [
                'label' => /** @Desc("Identifier") */ 'object_state.update.identifier',
            ])
            ->add('name', TextType::class, [
                'label' => /** @Desc("Name") */ 'object_state.update.name',
            ])
            ->add('save', SubmitType::class, [
                'label' => /** @Desc("Save") */ 'object_state.update.submit',
            ]);
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ObjectStateUpdateData::class,
            'translation_domain' => 'object_state',
        ]);
    }
}
