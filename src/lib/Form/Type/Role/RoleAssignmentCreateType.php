<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\Role;

use EzSystems\EzPlatformAdminUi\Form\Data\Role\RoleAssignmentCreateData;
use EzSystems\EzPlatformAdminUi\Form\Type\RedirectableForm;
use EzSystems\EzPlatformAdminUi\Form\Type\Section\SectionChoiceType;
use EzSystems\EzPlatformAdminUi\Form\Type\UniversalDiscoveryWidget\UniversalDiscoveryWidgetType;
use EzSystems\EzPlatformAdminUi\Form\Type\UserChoiceType;
use EzSystems\EzPlatformAdminUi\Form\Type\UserGroupChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoleAssignmentCreateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('groups',
                UserGroupChoiceType::class,
                [
                    'required' => false,
                    'multiple' => true,
                    'label' => /** @Desc("Group") */ 'role_assignment.groups',
                ]
            )
            ->add('users',
                UserChoiceType::class,
                [
                    'required' => false,
                    'multiple' => true,
                    'label' => /** @Desc("User") */ 'role_assignment.users',
                ]
            )
            ->add('sections',
                SectionChoiceType::class,
                [
                    'required' => false,
                    'multiple' => true,
                    'label' => /** @Desc("Section") */ 'role_assignment.sections',
                ]
            )
            ->add('locations',
                UniversalDiscoveryWidgetType::class,
                [
                    'required' => false,
                    'label' => /** @Desc("Choose Locations") */ 'role_assignment.locations',
                ]
            )
            ->add(
                'save',
                SubmitType::class,
                ['label' => /** @Desc("Assign") */ 'role_assignment.save']
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RoleAssignmentCreateData::class,
            'translation_domain' => 'forms',
        ]);
    }

    public function getParent()
    {
        return RedirectableForm::class;
    }
}
