<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Form\Type\ObjectState;

use Ibexa\AdminUi\Form\Data\ObjectState\ObjectStateDeleteData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ObjectStateDeleteType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('object_state_', ObjectStateType::class)
            ->add('delete', SubmitType::class, [
                'label' => /** @Desc("Delete") */
                    'object_state_.delete.submit',
            ]);
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ObjectStateDeleteData::class,
            'translation_domain' => 'object_state',
        ]);
    }
}

class_alias(ObjectStateDeleteType::class, 'EzSystems\EzPlatformAdminUi\Form\Type\ObjectState\ObjectStateDeleteType');
