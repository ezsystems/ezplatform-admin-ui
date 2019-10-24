<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\Type\FieldType;

use eZ\Publish\API\Repository\FieldTypeService;
use EzSystems\EzPlatformAdminUi\Form\DataTransformer\FieldType\FieldValueTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form Type representing ezstring field type.
 */
class TextLineFieldType extends AbstractType
{
    /** @var FieldTypeService */
    private $fieldTypeService;

    public function __construct(FieldTypeService $fieldTypeService)
    {
        $this->fieldTypeService = $fieldTypeService;
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'ezplatform_fieldtype_ezstring';
    }

    public function getParent()
    {
        return TextType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new FieldValueTransformer($this->fieldTypeService->getFieldType('ezstring')));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $attributes = [];

        if (null !== $options['min']) {
            $attributes['data-min'] = $options['min'];
        }

        if (null !== $options['max']) {
            $attributes['data-max'] = $options['max'];
        }

        $view->vars['attr'] = array_merge($view->vars['attr'], $attributes);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'min' => null,
            'max' => null,
        ])
            ->setAllowedTypes('min', ['integer', 'null'])
            ->setAllowedTypes('max', ['integer', 'null']);
    }
}
