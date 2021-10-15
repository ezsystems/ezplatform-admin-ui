<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\AdminUi\Form\Type\RoleAssignment;

use Ibexa\AdminUi\Form\Data\RoleAssignmentData;
use Ibexa\AdminUi\Form\Type\Section\SectionChoiceType;
use Ibexa\AdminUi\Form\Type\UniversalDiscoveryWidget\UniversalDiscoveryWidgetType;
use Ibexa\AdminUi\Form\Type\UserChoiceType;
use Ibexa\AdminUi\Form\Type\UserGroupChoiceType;
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
            'multiple' => true,
        ]);

        $builder->add('locations', UniversalDiscoveryWidgetType::class, [
            'required' => false,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RoleAssignmentData::class,
        ]);
    }
}

class_alias(RoleAssignmentType::class, 'EzSystems\EzPlatformAdminUi\Form\Type\RoleAssignment\RoleAssignmentType');
