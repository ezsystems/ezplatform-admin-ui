<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Form\Type\URLWildcard;

use Ibexa\AdminUi\Form\Data\URLWildcard\URLWildcardListData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * URLWildcard list form.
 */
final class URLWildcardListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('type', ChoiceType::class, [
            'choices' => [
                'url_wildcard.type.direct' => true,
                'url_wildcard.type.forward' => false,
            ],
            'placeholder' => 'url_wildcard.type.all',
            'required' => false,
        ]);

        $builder->add('searchQuery', SearchType::class, [
            'required' => false,
        ]);

        $builder->add('limit', HiddenType::class);
        $builder->add('page', HiddenType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => URLWildcardListData::class,
            'translation_domain' => 'ezplatform_url_wildcard',
        ]);
    }
}
