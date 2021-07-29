<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\ContentType;

use EzSystems\EzPlatformAdminUi\Form\Type\FieldDefinition\FieldDefinitionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class FieldTypePaletteButtonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $prototype = $builder->create(
            $options['prototype_name'],
            FieldDefinitionType::class,
            [
                'languageCode' => $options['language_code'],
                'mainLanguageCode' => $options['main_language_code'],
                'field_type_identifier' => $options['field_type_identifier'],
            ]
        )->getForm();

        $builder->setAttribute('prototype', $prototype);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $prototype = $form->getConfig()->getAttribute('prototype');
        if ($prototype instanceof FormInterface) {
            $view->vars['prototype'] = $prototype->createView();
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired([
            'field_type_identifier',
            'language_code',
            'main_language_code',
            'prototype_name',
        ]);

        $resolver->setDefaults([
            'prototype_name' => 'NAME_PLACEHOLDER',
        ]);
    }

    public function getParent(): string
    {
        return ButtonType::class;
    }
}
