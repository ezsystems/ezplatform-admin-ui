<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Form\Type\URLWildcard;

use Ibexa\AdminUi\Form\Data\URLWildcard\URLWildcardListData;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class URLWildcardListType extends AbstractType implements TranslationContainerInterface
{
    private const TYPE_DIRECT = 'url_wildcard.type.direct';
    private const TYPE_FORWARD = 'url_wildcard.type.forward';
    private const PLACEHOLDER = 'url_wildcard.type.all';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('type', ChoiceType::class, [
            'choices' => [
                self::TYPE_DIRECT => true,
                self::TYPE_FORWARD => false,
            ],
            'placeholder' => self::PLACEHOLDER,
            'required' => false,
        ]);

        $builder->add('searchQuery', SearchType::class, [
            'required' => false,
        ]);

        $builder->add('limit', HiddenType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => URLWildcardListData::class,
            'translation_domain' => 'ezplatform_url_wildcard',
        ]);
    }

    /**
     * @return array<\JMS\TranslationBundle\Model\Message>
     */
    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::TYPE_DIRECT, 'ezplatform_url_wildcard'))->setDesc('Direct'),
            (new Message(self::TYPE_FORWARD, 'ezplatform_url_wildcard'))->setDesc('Forward'),
            (new Message(self::PLACEHOLDER, 'ezplatform_url_wildcard'))->setDesc('All'),
        ];
    }
}
