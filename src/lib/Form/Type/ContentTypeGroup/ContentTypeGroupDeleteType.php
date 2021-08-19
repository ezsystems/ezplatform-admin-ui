<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Form\Type\ContentTypeGroup;

use Ibexa\AdminUi\Form\Data\ContentTypeGroup\ContentTypeGroupDeleteData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentTypeGroupDeleteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content_type_group', ContentTypeGroupType::class)
            ->add('delete', SubmitType::class, [
                'label' => /** @Desc("Delete") */ 'content_type_group.delete.submit',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ContentTypeGroupDeleteData::class,
            'translation_domain' => 'content_type',
        ]);
    }
}

class_alias(ContentTypeGroupDeleteType::class, 'EzSystems\EzPlatformAdminUi\Form\Type\ContentTypeGroup\ContentTypeGroupDeleteType');
