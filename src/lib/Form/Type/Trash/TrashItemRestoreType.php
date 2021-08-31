<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\Trash;

use EzSystems\EzPlatformAdminUi\Form\Data\Trash\TrashItemRestoreData;
use EzSystems\EzPlatformAdminUi\Form\Type\UniversalDiscoveryWidget\UniversalDiscoveryWidgetType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrashItemRestoreType extends AbstractType
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
            ],
            'label' => false,
            'allow_add' => true,
        ]);

        $builder->add('location', UniversalDiscoveryWidgetType::class, [
            'multiple' => false,
            'label' => false,
            'attr' => $options['attr'],
        ]);

        $builder->add('restore', SubmitType::class, [
            'label' => false,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TrashItemRestoreData::class,
            'translation_domain' => 'forms',
        ]);
    }
}
