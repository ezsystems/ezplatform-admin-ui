<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\ContentType;

use EzSystems\EzPlatformAdminUi\Form\Data\ContentType\DraftContentTypesDeleteData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DraftContentTypesDeleteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content_types', CollectionType::class, [
                'entry_type' => CheckboxType::class,
                'required' => false,
                'allow_add' => true,
                'label' => false,
                'entry_options' => ['label' => false],
            ])
            ->add('delete', SubmitType::class, [
                'attr' => ['hidden' => true],
                'label' => /** @Desc("Delete draft content types") */ 'draft_content_types_delete_form.delete',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DraftContentTypesDeleteData::class,
            'translation_domain' => 'forms',
        ]);
    }
}
