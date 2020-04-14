<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\User;

use eZ\Publish\API\Repository\Values\User\User;
use EzSystems\EzPlatformAdminUi\Form\Data\User\UserPasswordResetData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use EzSystems\EzPlatformAdminUi\Validator\Constraints\Password as NewPassword;

/**
 * @deprecated use \EzSystems\EzPlatformUser\Form\Type\UserPasswordResetType.
 */
class UserPasswordResetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $contentType = null;
        if ($options['user'] instanceof User) {
            $contentType = $options['user']->getContentType();
        }

        $builder
            ->add('new_password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => /** @Desc("Passwords do not match.") */ 'ezplatform.reset_user_password.passwords_must_match',
                'required' => true,
                'first_options' => [
                    'label' => /** @Desc("New password") */ 'ezplatform.reset_user_password.new_password',
                ],
                'second_options' => [
                    'label' => /** @Desc("Confirm password") */ 'ezplatform.reset_user_password.confirm_new_password',
                ],
                'constraints' => [
                    new NewPassword([
                        'contentType' => $contentType,
                    ]),
                ],
            ])
            ->add(
                'update',
                SubmitType::class,
                ['label' => /** @Desc("Update") */ 'ezplatform.reset_user_password.update']
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserPasswordResetData::class,
            'translation_domain' => 'forms',
            'user' => null,
        ]);

        $resolver->setAllowedTypes('user', ['null', User::class]);
    }
}
