<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\ContentType;

use EzSystems\EzPlatformAdminUi\Form\Data\ContentTypeMetadataData;
use EzSystems\EzPlatformAdminUi\Form\DataTransformer\TranslatablePropertyTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ContentTypeMetadataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isTranslation = $options['main_language_code'] !== $options['language_code'];

        $builder
            ->add(
                $builder
                    ->create('name', TextType::class, [
                        'property_path' => 'names',
                        'label' => /** @Desc("Name") */ 'content_type.name',
                    ])
                    ->addModelTransformer(new TranslatablePropertyTransformer($options['language_code']))
            )
            ->add('identifier', TextType::class, [
                'label' => /** @Desc("Identifier") */ 'content_type.identifier',
                'disabled' => $isTranslation,
            ])
            ->add(
                $builder
                    ->create('description', TextType::class, [
                        'property_path' => 'descriptions',
                        'required' => false,
                        'label' => /** @Desc("Description") */ 'content_type.description',
                    ])
                    ->addModelTransformer(new TranslatablePropertyTransformer($options['language_code']))
            )
            ->add('nameSchema', TextType::class, [
                'required' => false,
                'label' => /** @Desc("Content name pattern") */ 'content_type.name_schema',
                'disabled' => $isTranslation,
            ])
            ->add('urlAliasSchema', TextType::class, [
                'required' => false,
                'label' => /** @Desc("URL alias name pattern") */ 'content_type.url_alias_schema',
                'empty_data' => false,
                'disabled' => $isTranslation,
            ])
            ->add('isContainer', CheckboxType::class, [
                'required' => false,
                'label' => /** @Desc("Container") */ 'content_type.is_container',
                'disabled' => $isTranslation,
            ])
            ->add('defaultSortField', SortFieldChoiceType::class, [
                'label' => /** @Desc("Sort children by default by") */ 'content_type.default_sort_field',
                'disabled' => $isTranslation,
            ])
            ->add('defaultSortOrder', SortOrderChoiceType::class, [
                'label' => /** @Desc("Sort children by default in order") */ 'content_type.default_sort_order',
                'disabled' => $isTranslation,
            ])
            ->add('defaultAlwaysAvailable', CheckboxType::class, [
                'required' => false,
                'label' => /** @Desc("Make content available even with missing translations") */ 'content_type.default_always_available',
                'disabled' => $isTranslation,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContentTypeMetadataData::class,
            'translation_domain' => 'content_type',
            'main_language_code' => null,
        ]);

        $resolver->setAllowedTypes('main_language_code', ['null', 'string']);

        $resolver->setRequired(['language_code']);
        $resolver->setAllowedTypes('language_code', ['null', 'string']);
    }
}
