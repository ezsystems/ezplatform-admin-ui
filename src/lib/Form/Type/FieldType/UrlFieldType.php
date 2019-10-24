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
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form Type representing ezurl field type.
 */
class UrlFieldType extends AbstractType
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
        return 'ezplatform_fieldtype_ezurl';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'link',
                UrlType::class,
                [
                    'label' => /** @Desc("URL") */ 'content.field_type.ezurl.link',
                    'required' => $options['required'],
                ]
            )
            ->add(
                'text',
                TextType::class,
                [
                    'label' => /** @Desc("Text") */ 'content.field_type.ezurl.text',
                    'required' => false,
                ]
            )
            ->addModelTransformer(new FieldValueTransformer($this->fieldTypeService->getFieldType('ezurl')));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'ezrepoforms_content',
        ]);
    }
}
