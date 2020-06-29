<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\Search;

use EzSystems\EzPlatformAdminUi\Form\Data\Search\TrashSearchData;
use EzSystems\EzPlatformAdminUi\Form\Type\ChoiceList\Loader\DatePeriodChoiceLoader;
use EzSystems\EzPlatformAdminUi\Form\Type\Content\SortType;
use EzSystems\EzPlatformAdminUi\Form\Type\User\UserType;
use EzSystems\EzPlatformAdminUi\Form\Type\ContentType\ContentTypeChoiceType;
use EzSystems\EzPlatformAdminUi\Form\Type\Section\SectionChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\AbstractType;
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

    /** @var \EzSystems\EzPlatformAdminUi\Form\Type\ChoiceList\Loader\DatePeriodChoiceLoader */
    private $datePeriodChoiceLoader;

    public function __construct(
        TranslatorInterface $translator,
        PermissionResolver $permissionResolver,
        DatePeriodChoiceLoader $datePeriodChoiceLoader
    ) {
        $this->translator = $translator;
        $this->permissionResolver = $permissionResolver;
        $this->datePeriodChoiceLoader = $datePeriodChoiceLoader;
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('page', HiddenType::class)
            ->add('content_type', ContentTypeChoiceType::class, [
                'required' => false,
                'placeholder' => /** @Desc("Any Content Types") */ 'trash.search.any_content_types',
            ])
            ->add('creator', UserType::class)
            ->add('trashed_interval', DateIntervalType::class)
            ->add('trashed', ChoiceType::class, [
                'choice_loader' => $this->datePeriodChoiceLoader,
                'required' => false,
                'placeholder' => /** @Desc("Any time") */ 'trash.search.any_time',
                'mapped' => false,
            ])
            ->add('sort', SortType::class, [
                'sort_fields' => ['name', 'content_type', 'creator', 'section', 'parent_location', 'trashed'],
                'default' => ['field' => 'trashed', 'direction' => '1'],
            ])
        ;

        if ($this->permissionResolver->hasAccess('section', 'view') !== false) {
            $builder->add('section', SectionChoiceType::class, [
                'required' => false,
                'multiple' => false,
                'placeholder' => /** @Desc("Any Section") */ 'trash.search.section.any',
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
            'method' => Request::METHOD_GET,
            'csrf_protection' => false,
        ]);
    }
}
