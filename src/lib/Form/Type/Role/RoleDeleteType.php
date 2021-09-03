<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Form\Type\Role;

use Ibexa\AdminUi\Form\Data\Role\RoleDeleteData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoleDeleteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'role',
                RoleType::class,
                ['label' => false]
            )
            ->add(
                'delete',
                SubmitType::class,
                ['label' => /** @Desc("Delete") */ 'role_delete.delete']
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RoleDeleteData::class,
            'translation_domain' => 'forms',
        ]);
    }
}

class_alias(RoleDeleteType::class, 'EzSystems\EzPlatformAdminUi\Form\Type\Role\RoleDeleteType');
