<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\User;

use eZ\Publish\API\Repository\Values\User\User;
use EzSystems\EzPlatformAdminUi\Form\Data\User\UserPasswordChangeData;
use EzSystems\EzPlatformAdminUi\Validator\Constraints\Password;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @deprecated Since eZ Platform 3.0.2 class moved to EzPlatformUser Bundle. Use it instead.
 * @see \EzSystems\EzPlatformUser\Form\Type\UserPasswordChangeType.
 */
class UserPasswordChangeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $contentType = null;
        if ($options['user'] instanceof User) {
            $contentType = $options['user']->getContentType();
        }

        $builder
            ->add('oldPassword', PasswordType::class, [
                'required' => true,
                'label' => /** @Desc("Current password") */ 'ezplatform.change_user_password.old_password',
            ])
            ->add('newPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => /** @Desc("Passwords do not match.") */ 'ezplatform.change_user_password.passwords_must_match',
                'required' => true,
                'constraints' => [
                    new Password([
                        'contentType' => $contentType,
                    ]),
                ],
                'first_options' => [
                    'label' => /** @Desc("New password") */ 'ezplatform.change_user_password.new_password',
                ],
                'second_options' => [
                    'label' => /** @Desc("Confirm password") */ 'ezplatform.change_user_password.confirm_new_password',
                ],
            ])
            ->add(
                'change',
                SubmitType::class,
                ['label' => /** @Desc("Change") */ 'ezplatform.change_user_password.change']
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserPasswordChangeData::class,
            'translation_domain' => 'forms',
            'user' => null,
        ]);

        $resolver->setAllowedTypes('user', ['null', User::class]);
    }
}
