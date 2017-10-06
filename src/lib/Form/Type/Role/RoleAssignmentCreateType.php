<?php
declare(strict_types=1);

namespace EzPlatformAdminUi\Form\Type\Role;

use EzPlatformAdminUi\Form\Data\Role\RoleAssignmentCreateData;
use EzPlatformAdminUi\Form\Type\Section\SectionChoiceType;
use EzPlatformAdminUi\Form\Type\UniversalDiscoveryWidget\UniversalDiscoveryWidgetType;
use EzPlatformAdminUi\Form\Type\UserChoiceType;
use EzPlatformAdminUi\Form\Type\UserGroupChoiceType;
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
                    'label' => /** @Desc("Location") */ 'role_assignment.locations',
                ]
            )
            ->add(
                'save',
                SubmitType::class,
                ['label' => /** @Desc("Create") */ 'role_assignment.save']
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RoleAssignmentCreateData::class,
            'translation_domain' => 'forms',
        ]);
    }
}
