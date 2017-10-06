<?php

namespace EzPlatformAdminUi\Form\Type\Language;

use EzPlatformAdminUi\Form\Data\Language\LanguageCreateData;
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
                ['label' => /** @Desc("Name") */ 'language_create.name']
            )
            ->add(
                'languageCode',
                TextType::class,
                ['label' => /** @Desc("Language code") */ 'language_create.language_code']
            )
            ->add('enabled',
                CheckboxType::class,
                [
                    'label' => /** @Desc("Enabled") */ 'language_create.enabled',
                    'required' => false,
                ]
            )
            ->add(
                'save',
                SubmitType::class,
                ['label' => /** @Desc("Create") */ 'language_create.save']
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
