<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\Content\Draft;

use EzSystems\EzPlatformAdminUi\Form\Data\Content\Draft\ContentRemoveData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentRemoveType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'versions',
            CollectionType::class,
            [
                'entry_type' => CheckboxType::class,
                'required' => false,
                'allow_add' => true,
                'entry_options' => [
                    'label' => false,
                ],
                'label' => false,
            ]
        );

        $builder->add(
            'remove',
            SubmitType::class
        );
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContentRemoveData::class,
        ]);
    }
}
