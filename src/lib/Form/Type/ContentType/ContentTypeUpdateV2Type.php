<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\ContentType;

use EzSystems\EzPlatformAdminUi\Form\Data\ContentTypeUpdateV2Data;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ContentTypeUpdateV2Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isTranslation = $options['language_code'] !== $options['main_language_code'];

        $builder->add('metadata', ContentTypeMetadataType::class, [
            'language_code' => $options['language_code'],
            'main_language_code' => $options['main_language_code'],
        ]);

        $builder->add('fieldDefinitions', FieldDefinitionCollectionType::class, [
            'language_code' => $options['language_code'],
            'main_language_code' => $options['main_language_code'],
        ]);

        $builder->add('fieldTypePalette', FieldTypePaletteType::class, [
            'mapped' => false,
            'label' => /** @Desc("Field Type selection") */ 'content_type.field_type_selection',
            'disabled' => $isTranslation,
            'language_code' => $options['language_code'],
            'main_language_code' => $options['main_language_code'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContentTypeUpdateV2Data::class,
            'translation_domain' => 'content_type',
            'main_language_code' => null,
        ]);

        $resolver->setAllowedTypes('main_language_code', ['null', 'string']);

        $resolver->setRequired(['language_code']);
        $resolver->setAllowedTypes('language_code', ['null', 'string']);
    }
}
