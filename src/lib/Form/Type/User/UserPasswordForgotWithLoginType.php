<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\User;

use EzSystems\EzPlatformAdminUi\Form\Data\User\UserPasswordForgotWithLoginData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @deprecated Since eZ Platform 3.0 class moved to EzPlatformUser Bundle. Use it instead.
 *
 * @see \EzSystems\EzPlatformUser\Form\Type\UserPasswordForgotWithLoginType.
 */
class UserPasswordForgotWithLoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('login', TextType::class, [
                'required' => true,
                'label' => /** @Desc("Enter your login:") */ 'ezplatform.forgot_user_password.login',
            ])
            ->add(
                'reset',
                SubmitType::class,
                ['label' => /** @Desc("Reset") */ 'ezplatform.forgot_user_password.reset']
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserPasswordForgotWithLoginData::class,
            'translation_domain' => 'forms',
        ]);
    }
}
