<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\Type\FieldType;

use eZ\Publish\API\Repository\FieldTypeService;
use EzSystems\EzPlatformAdminUi\Form\DataTransformer\FieldType\FieldValueTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form Type representing ezinteger field type.
 */
class IntegerFieldType extends AbstractType
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
        return 'ezplatform_fieldtype_ezinteger';
    }

    public function getParent()
    {
        return IntegerType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $attributes = ['step' => 1];

        if (null !== $options['min']) {
            $attributes['min'] = $options['min'];
        }

        if (null !== $options['max']) {
            $attributes['max'] = $options['max'];
        }

        $builder->addModelTransformer(new FieldValueTransformer($this->fieldTypeService->getFieldType('ezinteger')));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $attributes = ['step' => 1];

        if (null !== $options['min']) {
            $attributes['min'] = $options['min'];
        }

        if (null !== $options['max']) {
            $attributes['max'] = $options['max'];
        }

        $view->vars['attr'] = array_merge($view->vars['attr'], $attributes);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(['min' => null, 'max' => null])
            ->setAllowedTypes('min', ['integer', 'null'])
            ->setAllowedTypes('max', ['integer', 'null']);
    }
}
