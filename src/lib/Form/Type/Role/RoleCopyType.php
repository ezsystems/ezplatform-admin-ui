<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\Role;

use EzSystems\EzPlatformAdminUi\Form\Data\Role\RoleCopyData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoleCopyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'new_identifier',
                TextType::class,
                ['label' => /** @Desc("Name of a new role") */ 'role_copy.name']
            )
            ->add(
                'copy',
                SubmitType::class,
                ['label' => /** @Desc("Copy") */ 'role_copy.copy']
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RoleCopyData::class,
            'translation_domain' => 'forms',
        ]);
    }
}
