<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\Type\FieldType;

use eZ\Publish\API\Repository\FieldTypeService;
use EzSystems\EzPlatformAdminUi\Form\DataTransformer\FieldType\FieldValueTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form Type representing ezgmaplocation field type.
 */
class MapLocationFieldType extends AbstractType
{
    /** @var FieldTypeService */
    protected $fieldTypeService;

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
        return 'ezplatform_fieldtype_ezgmaplocation';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'latitude',
                NumberType::class,
                [
                    'label' => /** @Desc("Latitude") */ 'content.field_type.ezgmaplocation.latitude',
                    'required' => $options['required'],
                    'scale' => 6,
                    'attr' => [
                        'min' => -90,
                        'max' => 90,
                        'step' => 0.000001,
                    ],
                ]
            )
            ->add(
                'longitude',
                NumberType::class,
                [
                    'label' => /** @Desc("Longitude") */ 'content.field_type.ezgmaplocation.longitude',
                    'required' => $options['required'],
                    'scale' => 6,
                    'attr' => [
                        'min' => -90,
                        'max' => 90,
                        'step' => 0.000001,
                    ],
                ]
            )
            ->add(
                'address',
                TextType::class,
                [
                    'label' => /* @Desc("Address") */ 'content.field_type.ezgmaplocation.address',
                    'required' => false,
                    'empty_data' => '',
                ]
            )
            ->addModelTransformer(
                new FieldValueTransformer($this->fieldTypeService->getFieldType('ezgmaplocation'))
            );
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view->children['latitude']->vars['type'] = 'number';
        $view->children['longitude']->vars['type'] = 'number';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['translation_domain' => 'ezrepoforms_fieldtype']);
    }
}
