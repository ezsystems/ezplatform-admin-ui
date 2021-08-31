<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\ObjectState;

use EzSystems\EzPlatformAdminUi\Form\Data\ObjectState\ObjectStateGroupsDeleteData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ObjectStateGroupsDeleteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('objectStateGroups', CollectionType::class, [
                'entry_type' => CheckboxType::class,
                'required' => false,
                'allow_add' => true,
                'label' => false,
                'entry_options' => ['label' => false],
            ])
            ->add('delete', SubmitType::class, [
                'attr' => ['hidden' => true],
                'label' => /** @Desc("Delete Object state group") */ 'object_state_group.bulk_delete.delete',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ObjectStateGroupsDeleteData::class,
            'translation_domain' => 'object_state',
        ]);
    }
}
