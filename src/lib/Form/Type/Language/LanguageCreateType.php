<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\Type\Language;

use EzSystems\EzPlatformAdminUi\Form\Data\Language\LanguageCreateData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LanguageCreateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                TextType::class,
                ['label' => /** @Desc("Name") */ 'ezplatform.language.create.name']
            )
            ->add(
                'languageCode',
                TextType::class,
                ['label' => /** @Desc("Language code") */ 'ezplatform.language.create.language_code']
            )
            ->add('enabled',
                CheckboxType::class,
                [
                    'label' => /** @Desc("Enabled") */ 'ezplatform.language.create.enabled',
                    'required' => false,
                ]
            )
            ->add(
                'save',
                SubmitType::class,
                ['label' => /** @Desc("Create") */ 'ezplatform.language.create.save']
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => LanguageCreateData::class,
            'translation_domain' => 'forms',
        ]);
    }
}
