<?php

namespace EzPlatformAdminUi\Form\Type\RoleAssignment;

use EzPlatformAdminUi\Form\Data\RoleAssignmentData;
use EzPlatformAdminUi\Form\Type\Section\SectionChoiceType;
use EzPlatformAdminUi\Form\Type\UniversalDiscoveryWidget\UniversalDiscoveryWidgetType;
use EzPlatformAdminUi\Form\Type\UserChoiceType;
use EzPlatformAdminUi\Form\Type\UserGroupChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoleAssignmentType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('groups', UserGroupChoiceType::class, [
            'required' => false,
            'multiple' => true,
        ]);

        $builder->add('users', UserChoiceType::class, [
            'required' => false,
            'multiple' => true,
        ]);

        $builder->add('sections', SectionChoiceType::class, [
            'required' => false,
            'multiple' => true
        ]);

        $builder->add('locations', UniversalDiscoveryWidgetType::class, [
            'required' => false
        ]);
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RoleAssignmentData::class
        ]);
    }
}
