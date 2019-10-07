<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\Type\FieldType;

use eZ\Publish\API\Repository\FieldTypeService;
use EzSystems\EzPlatformAdminUi\Form\DataTransformer\FieldType\FieldValueTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Form Type representing ezboolean field type.
 */
class CheckboxFieldType extends AbstractType
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
        return 'ezplatform_fieldtype_ezboolean';
    }

    public function getParent()
    {
        return CheckboxType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addModelTransformer(new FieldValueTransformer($this->fieldTypeService->getFieldType('ezboolean')));
    }
}
