<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\Type\Content;

use EzSystems\EzPlatformAdminUi\Form\EventSubscriber\SuppressValidationSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use eZ\Publish\API\Repository\Values\Content\ContentStruct;

/**
 * Form type for content edition (create/update).
 * Underlying data will be either \EzSystems\RepositoryForms\Data\Content\ContentCreateData or \EzSystems\RepositoryForms\Data\Content\ContentUpdateData
 * depending on the context (create or update).
 */
class ContentEditType extends AbstractType
{
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'ezrepoforms_content_edit';
    }

    public function getParent()
    {
        return BaseContentType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('publish', SubmitType::class, ['label' => 'content.publish_button']);

        if ($options['drafts_enabled']) {
            $builder
                ->add('saveDraft', SubmitType::class, ['label' => 'content.save_button'])
                ->add('cancel', SubmitType::class, [
                    'label' => 'content.cancel_button',
                    'attr' => ['formnovalidate' => 'formnovalidate'],
                ]);
            $builder->addEventSubscriber(new SuppressValidationSubscriber());
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'drafts_enabled' => false,
                'data_class' => ContentStruct::class,
                'translation_domain' => 'ezrepoforms_content',
                'intent' => 'update',
            ]);
    }
}
