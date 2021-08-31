<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\Content;

use EzSystems\EzPlatformAdminUi\Form\Data\Content\ContentVisibilityUpdateData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentVisibilityUpdateType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'contentInfo',
                ContentInfoType::class,
                ['label' => false, 'attr' => ['hidden' => true]]
            )
            ->add(
                'location',
                LocationType::class,
                ['label' => false, 'attr' => ['hidden' => true]]
            )
            ->add(
                'visible',
                HiddenType::class
            );
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ContentVisibilityUpdateData::class,
        ]);
    }
}
