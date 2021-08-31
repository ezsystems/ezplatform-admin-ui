<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\Type;

use EzSystems\EzPlatformAdminUi\Form\DataTransformer\DateIntervalToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Form type for ContentType update.
 */
class DateTimeIntervalType extends AbstractType
{
    public function getParent()
    {
        return FormType::class;
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'datetimeinterval';
    }

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addViewTransformer(new DateIntervalToArrayTransformer())
            ->add('year', IntegerType::class)
            ->add('month', IntegerType::class)
            ->add('day', IntegerType::class)
            ->add('hour', IntegerType::class)
            ->add('minute', IntegerType::class)
            ->add('second', IntegerType::class);
    }
}
