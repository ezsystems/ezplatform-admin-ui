<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\Trash;

use EzSystems\EzPlatformAdminUi\Form\Data\Trash\TrashItemDeleteData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrashItemDeleteType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('trash_items', CollectionType::class, [
            'entry_type' => TrashItemCheckboxType::class,
            'entry_options' => [
                'required' => false,
                'attr' => ['hidden' => true],
            ],
            'label' => false,
            'allow_add' => true,
        ]);

        $builder->add('delete', SubmitType::class, [
            'label' => false,
            'attr' => ['hidden' => true],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TrashItemDeleteData::class,
            'translation_domain' => 'forms',
        ]);
    }
}
