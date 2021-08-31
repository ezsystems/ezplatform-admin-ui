<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\Content\Translation;

use EzSystems\EzPlatformAdminUi\Form\Data\Content\Translation\MainTranslationUpdateData;
use EzSystems\EzPlatformAdminUi\Form\Type\Content\ContentType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MainTranslationUpdateType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'content',
                ContentType::class,
                ['label' => false]
            )
            ->add(
                'language_code',
                HiddenType::class,
                ['label' => false]
            )
            ->add(
                'update',
                SubmitType::class,
                [
                    'attr' => ['hidden' => true],
                    'label' => false,
                ]
            );
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MainTranslationUpdateData::class,
            'translation_domain' => 'forms',
        ]);
    }
}
