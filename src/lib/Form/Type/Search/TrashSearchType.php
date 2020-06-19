<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\Search;

use EzSystems\EzPlatformAdminUi\Form\Data\Search\TrashSearchData;
use EzSystems\EzPlatformAdminUi\Form\Type\User\UserType;
use EzSystems\EzPlatformAdminUi\Form\Type\ContentType\ContentTypeChoiceType;
use EzSystems\EzPlatformAdminUi\Form\Type\Section\SectionChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use eZ\Publish\API\Repository\PermissionResolver;

class TrashSearchType extends AbstractType
{
    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
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
            ->add('page', HiddenType::class)
            ->add('limit', HiddenType::class)
            ->add('content_type', ContentTypeChoiceType::class, [
                'required' => false,
                'placeholder' => /** @Desc("Any content types") */ 'trash.search.any_content_types',
            ])
            ->add('creator', UserType::class)
            ->add('trashed_interval', DateIntervalType::class)
            ->add('trashed', ChoiceType::class, [
                'choices' => $this->getTimePeriodChoices(), // todo: choiceloader
                'required' => false,
                'placeholder' => /** @Desc("Any time") */ 'trash.search.any_time',
                'mapped' => false,
            ])
            ->add('sort', SortType::class)
        ;

        if ($this->permissionResolver->hasAccess('section', 'view') !== false) {
            $builder->add('section', SectionChoiceType::class, [
                'required' => false,
                'multiple' => false,
                'placeholder' => /** @Desc("Any section") */ 'trash.search.section.any',
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
            'data_class' => TrashSearchData::class,
//            'error_mapping' => [
//                'created' => 'created_select',
//                'last_modified' => 'last_modified_select',
//            ],
            'method' => Request::METHOD_GET,
            'csrf_protection' => false,
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
