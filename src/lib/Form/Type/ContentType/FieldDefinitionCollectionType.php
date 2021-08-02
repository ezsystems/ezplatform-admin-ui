<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\ContentType;

use EzSystems\EzPlatformAdminUi\Form\Type\FieldDefinition\FieldDefinitionV2Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class FieldDefinitionCollectionType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => true,
            'entry_type' => FieldDefinitionV2Type::class,
            'entry_options' => static function (Options $options): array {
                return [
                    'language_code' => $options['language_code'],
                    'main_language_code' => $options['main_language_code'],
                ];
            },
            'main_language_code' => null,
            'prototype' => false,
            'translation_domain' => 'content_type',
        ]);

        $resolver->setAllowedTypes('main_language_code', ['null', 'string']);

        $resolver->setRequired(['language_code']);
        $resolver->setAllowedTypes('language_code', ['null', 'string']);
    }

    public function getParent(): string
    {
        return CollectionType::class;
    }
}
