<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\Type\URL;

use EzSystems\EzPlatformAdminUi\Form\Data\URL\URLListData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * URL list form.
 */
class URLListType extends AbstractType
{
    /**
     * @var \Symfony\Contracts\Translation\TranslatorInterface
     */
    private $translator;

    /**
     * URLListType constructor.
     *
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('status', ChoiceType::class, [
            'choices' => [
                $this->translator->trans(/** @Desc("Invalid") */ 'url.status.invalid', [], 'ezplatform_content_forms_url') => false,
                $this->translator->trans(/** @Desc("Valid") */ 'url.status.valid', [], 'ezplatform_content_forms_url') => true,
            ],
            'placeholder' => $this->translator->trans(/** @Desc("All") */ 'url.status.all', [], 'ezplatform_content_forms_url'),
            'required' => false,
        ]);

        $builder->add('searchQuery', SearchType::class, [
            'required' => false,
        ]);

        $builder->add('limit', HiddenType::class);
        $builder->add('page', HiddenType::class);
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => URLListData::class,
            'translation_domain' => 'ezplatform_content_forms_url',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * @inheritdoc
     */
    public function getBlockPrefix()
    {
        return 'ezplatform_content_forms_url_list';
    }
}
