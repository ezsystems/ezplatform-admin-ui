<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\Type\FieldType;

use EzSystems\EzPlatformAdminUi\Form\DataTransformer\FieldType\MultiSelectionValueTransformer;
use EzSystems\EzPlatformAdminUi\Form\DataTransformer\FieldType\SingleSelectionValueTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form Type representing ezselection field type.
 */
class SelectionFieldType extends AbstractType
{
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'ezplatform_fieldtype_ezselection';
    }

    public function getParent()
    {
        return ChoiceType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(
            $options['multiple'] ?
                new MultiSelectionValueTransformer() :
                new SingleSelectionValueTransformer()
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'expanded' => false,
        ]);
    }
}
