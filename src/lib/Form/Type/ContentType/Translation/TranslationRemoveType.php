<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\ContentType\Translation;

use EzSystems\EzPlatformAdminUi\Form\Data\ContentType\Translation\TranslationRemoveData;
use EzSystems\EzPlatformAdminUi\Form\Type\Content\ContentTypeType;
use EzSystems\EzPlatformAdminUi\Form\Type\ContentTypeGroup\ContentTypeGroupType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TranslationRemoveType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'content_type',
                ContentTypeType::class,
                [
                    'label' => false,
                    'attr' => [
                        'hidden' => true,
                    ],
                ]
            )
            ->add(
                'content_type_group',
                ContentTypeGroupType::class,
                ['label' => false]
            )
            ->add(
                'language_codes',
                CollectionType::class,
                [
                    'label' => false,
                    'allow_add' => true,
                    'entry_type' => CheckboxType::class,
                    'entry_options' => ['label' => false, 'required' => false],
                ]
            )
            ->add(
                'remove',
                SubmitType::class,
                [
                    'attr' => ['hidden' => true],
                    'label' => /** @Desc("Remove translation") */ 'content_translation_remove_form.remove',
                ]
            );
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TranslationRemoveData::class,
            'translation_domain' => 'forms',
        ]);
    }
}
