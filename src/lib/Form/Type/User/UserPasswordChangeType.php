<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\User;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\User\User;
use EzSystems\EzPlatformAdminUi\Form\Data\User\UserPasswordChangeData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use EzSystems\RepositoryForms\Validator\Constraints\Password;

class UserPasswordChangeType extends AbstractType
{
    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /**
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     */
    public function __construct(ContentTypeService $contentTypeService)
    {
        $this->contentTypeService = $contentTypeService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $contentType = null;
        if ($options['user'] instanceof User) {
            // TODO: Refactor to use $user->getContentType() when https://jira.ez.no/browse/EZP-29613 will be merged
            $contentType = $this->contentTypeService->loadContentType(
                $options['user']->contentInfo->contentTypeId
            );
        }

        $builder
            ->add('oldPassword', PasswordType::class, [
                'required' => true,
                'label' => /** @Desc("Old password") */ 'ezplatform.change_user_password.old_password',
            ])
            ->add('newPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => /** @Desc("The password fields must match.") */ 'ezplatform.change_user_password.passwords_must_match',
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
