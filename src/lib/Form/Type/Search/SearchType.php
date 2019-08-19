<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\Type\Search;

use EzSystems\EzPlatformAdminUi\Form\Data\Search\SearchData;
use EzSystems\EzPlatformAdminUi\Form\Type\User\UserType;
use EzSystems\EzPlatformAdminUi\Form\Type\ContentType\ContentTypeChoiceType;
use EzSystems\EzPlatformAdminUi\Form\Type\Section\SectionChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SearchType as CoreSearchType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use eZ\Publish\API\Repository\PermissionResolver;

class SearchType extends AbstractType
{
    /** @var \Symfony\Component\Translation\TranslatorInterface */
    private $translator;

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    private $permissionResolver;

    public function __construct(TranslatorInterface $translator, PermissionResolver $permissionResolver)
    {
        $this->translator = $translator;
        $this->permissionResolver = $permissionResolver;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('query', CoreSearchType::class, ['required' => false])
            ->add('page', HiddenType::class)
            ->add('limit', HiddenType::class)
            ->add('content_types', ContentTypeChoiceType::class, [
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('last_modified', DateIntervalType::class)
            ->add('created', DateIntervalType::class)
            ->add('creator', UserType::class)
            ->add('last_modified_select', ChoiceType::class, [
                'choices' => $this->getTimePeriodChoices(),
                'required' => false,
                'placeholder' => /** @Desc("Any time") */ 'search.any_time',
                'mapped' => false,
            ])
            ->add('created_select', ChoiceType::class, [
                'choices' => $this->getTimePeriodChoices(),
                'required' => false,
                'placeholder' => /** @Desc("Any time") */ 'search.any_time',
                'mapped' => false,
            ])
            ->add('subtree', HiddenType::class, [
                'required' => false,
            ])
        ;

        if ($this->permissionResolver->hasAccess('section', 'view') !== false) {
            $builder->add('section', SectionChoiceType::class, [
                'required' => false,
                'multiple' => false,
                'placeholder' => /** @Desc("Any section") */ 'search.section.any',
            ]);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SearchData::class,
            'error_mapping' => [
                'created' => 'created_select',
                'last_modified' => 'last_modified_select',
            ],
        ]);
    }

    /**
     * Generate time periods options available to choose.
     *
     * @return array
     *
     * @throws \Symfony\Component\Translation\Exception\InvalidArgumentException
     */
    private function getTimePeriodChoices(): array
    {
        $choices = [];
        foreach ($this->getTimePeriodField() as $label => $value) {
            $choices[$label] = $value;
        }

        return $choices;
    }

    /**
     * Returns available time periods values.
     *
     * @return array
     *
     * @throws \Symfony\Component\Translation\Exception\InvalidArgumentException
     */
    private function getTimePeriodField(): array
    {
        return [
            $this->translator->trans(/** @Desc("Last week") */ 'search.last_week', [], 'search') => 'P0Y0M7D',
            $this->translator->trans(/** @Desc("Last month") */ 'search.last_month', [], 'search') => 'P0Y1M0D',
            $this->translator->trans(/** @Desc("Last year") */ 'search.last_year', [], 'search') => 'P1Y0M0D',
            $this->translator->trans(/** @Desc("Custom range") */ 'search.custom_range', [], 'search') => 'custom_range',
        ];
    }
}
