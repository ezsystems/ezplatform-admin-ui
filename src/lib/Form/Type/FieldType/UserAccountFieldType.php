<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\Type\FieldType;

use EzSystems\EzPlatformAdminUi\Form\Data\User\UserAccountFieldData;
use EzSystems\EzPlatformAdminUi\Form\Type\SwitcherType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserAccountFieldType extends AbstractType
{
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'ezplatform_fieldtype_ezuser';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $isUpdateForm = 'update' === $options['intent'];

        $builder
            ->add('username', TextType::class, [
                'label' => /** @Desc("Username") */ 'content.field_type.ezuser.username',
                'required' => true,
                'attr' => $isUpdateForm ? ['disabled' => 'disabled'] : [],
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => !$isUpdateForm,
                'first_options' => ['label' => /** @Desc("Password") */ 'content.field_type.ezuser.password'],
                'second_options' => ['label' => /** @Desc("Confirm password") */ 'content.field_type.ezuser.password_confirm'],
            ])
            ->add('email', EmailType::class, [
                'required' => true,
                'label' => /** @Desc("Email") */ 'content.field_type.ezuser.email',
            ]);

        if (in_array($options['intent'], ['create', 'update'], true)) {
            $builder->add('enabled', SwitcherType::class, [
                'required' => false,
                'label' => /** @Desc("Enabled") */ 'content.field_type.ezuser.enabled',
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => UserAccountFieldData::class,
                'translation_domain' => 'ezrepoforms_content',
            ])
            ->setRequired(['intent'])
            ->setAllowedValues('intent', ['register', 'create', 'update']);
    }
}
