<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\Type\User;

use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\User\UserCreateData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form type for content edition (create/update).
 * Underlying data will be either \EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Content\ContentCreateData or \EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Content\ContentUpdateData
 * depending on the context (create or update).
 */
class UserCreateType extends AbstractType
{
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'ezrepoforms_user_create';
    }

    public function getParent()
    {
        return BaseUserType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('create', SubmitType::class, ['label' => /** @Desc("Create") */ 'user.create']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => UserCreateData::class,
                'intent' => 'create',
                'translation_domain' => 'ezrepoforms_user',
            ]);
    }
}
