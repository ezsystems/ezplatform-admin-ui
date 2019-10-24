<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\Type\User;

use EzSystems\EzPlatformAdminUi\Form\EventSubscriber\SuppressValidationSubscriber;
use EzSystems\EzPlatformAdminUi\Form\EventSubscriber\UserFieldsSubscriber;
use EzSystems\EzPlatformAdminUi\Form\Type\Content\BaseContentType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form type for content edition (create/update).
 * Underlying data will be either \EzSystems\RepositoryForms\Data\Content\ContentCreateData or \EzSystems\RepositoryForms\Data\Content\ContentUpdateData
 * depending on the context (create or update).
 */
class BaseUserType extends AbstractType
{
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'ezrepoforms_user';
    }

    public function getParent()
    {
        return BaseContentType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cancel', SubmitType::class, [
                'label' => /** @Desc("Cancel") */ 'user.cancel',
                'attr' => ['formnovalidate' => 'formnovalidate'],
            ])
            ->addEventSubscriber(new UserFieldsSubscriber())
            ->addEventSubscriber(new SuppressValidationSubscriber());
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'translation_domain' => 'ezrepoforms_user',
            ])
            ->setRequired([
                'languageCode',
                'intent',
            ])
            ->setAllowedValues('intent', ['update', 'create', 'register']);
    }
}
