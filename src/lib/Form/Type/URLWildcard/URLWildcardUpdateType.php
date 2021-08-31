<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\URLWildcard;

use EzSystems\EzPlatformAdminUi\Form\Data\URLWildcard\URLWildcardUpdateData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class URLWildcardUpdateType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('destination_url', TextType::class, [
                'label' => /** @Desc("Destination URL") */ 'url_wildcard.create.identifier',
            ])
            ->add('source_url', TextType::class, [
                'label' => /** @Desc("Source URL") */ 'url_wildcard.create.source_url',
            ])
            ->add('forward', CheckboxType::class, [
                'label' => false,
                'required' => false,
            ]);
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => URLWildcardUpdateData::class,
            'translation_domain' => 'url_wildcard',
        ]);
    }
}
