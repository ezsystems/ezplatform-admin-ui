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
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * URLWildcard list form.
 */
final class URLWildcardListType extends AbstractType
{
    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('type', ChoiceType::class, [
            'choices' => [
                $this->translator->trans(/** @Desc("Direct") */ 'url_wildcard.type.direct', [], 'ezplatform_url_wildcard') => true,
                $this->translator->trans(/** @Desc("Forward") */ 'url_wildcard.type.forward', [], 'ezplatform_url_wildcard') => false,
            ],
            'placeholder' => $this->translator->trans(/** @Desc("All") */ 'url_wildcard.type.all', [], 'ezplatform_url_wildcard'),
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
