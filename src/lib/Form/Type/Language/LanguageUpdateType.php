<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\Type\Language;

use EzSystems\EzPlatformAdminUi\Form\Data\Language\LanguageUpdateData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LanguageUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'language',
                LanguageType::class,
                ['label' => false]
            )
            ->add(
                'name',
                TextType::class,
                ['label' => /** @Desc("Name") */ 'ezplatform.language.update.name']
            )
            ->add('enabled',
                CheckboxType::class,
                [
                    'label' => /** @Desc("Enabled") */ 'ezplatform.language.update.enabled',
                    'required' => false,
                ]
            )
            ->add(
                'save',
                SubmitType::class,
                ['label' => /** @Desc("Save") */ 'ezplatform.language.update.save']
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => LanguageUpdateData::class,
            'translation_domain' => 'forms',
        ]);
    }
}
