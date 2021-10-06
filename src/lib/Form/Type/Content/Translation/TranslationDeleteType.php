<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Form\Type\Content\Translation;

use Ibexa\AdminUi\Form\Data\Content\Translation\TranslationDeleteData;
use Ibexa\AdminUi\Form\Type\Content\ContentInfoType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TranslationDeleteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'content_info',
                ContentInfoType::class,
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

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TranslationDeleteData::class,
            'translation_domain' => 'forms',
        ]);
    }
}

class_alias(TranslationDeleteType::class, 'EzSystems\EzPlatformAdminUi\Form\Type\Content\Translation\TranslationDeleteType');
