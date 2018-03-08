<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\Content\CustomUrl;

use eZ\Publish\API\Repository\LanguageService;
use EzSystems\EzPlatformAdminUi\Form\EventListener\AddLanguageFieldBasedOnContentListener;
use EzSystems\EzPlatformAdminUi\Form\EventListener\BuildPathFromRootListener;
use EzSystems\EzPlatformAdminUi\Form\Type\Content\LocationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;

class CustomUrlAddType extends AbstractType
{
    /** @var LanguageService */
    private $languageService;

    /** @var AddLanguageFieldBasedOnContentListener */
    private $addLanguageFieldBasedOnContentListener;

    /** @var BuildPathFromRootListener */
    private $buildPathFromRootListener;

    public function __construct(
        LanguageService $languageService,
        AddLanguageFieldBasedOnContentListener $addLanguageFieldBasedOnContentListener,
        BuildPathFromRootListener $buildPathFromRootListener
    ) {
        $this->languageService = $languageService;
        $this->addLanguageFieldBasedOnContentListener = $addLanguageFieldBasedOnContentListener;
        $this->buildPathFromRootListener = $buildPathFromRootListener;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'location',
                LocationType::class,
                ['label' => false]
            )
            ->add(
                'path',
                TextType::class,
                ['label' => false]
            )
            ->add(
                'language',
                ChoiceType::class,
                [
                    'multiple' => false,
                    'choice_loader' => new CallbackChoiceLoader([$this->languageService, 'loadLanguages']),
                    'choice_value' => 'languageCode',
                    'choice_label' => 'name',
                ]
            )
            ->add(
                'redirect',
                CheckboxType::class,
                [
                    'required' => false,
                    'label' => false,
                ]
            )
            ->add(
                'site_root',
                CheckboxType::class,
                [
                    'required' => false,
                    'label' => false,
                ]
            )
            ->add(
                'add',
                SubmitType::class,
                [
                    'label' => /** @Desc("Create") */ 'custom_url_alias_add_form.add',
                ]
            );

        $builder->addEventListener(FormEvents::PRE_SET_DATA, [
            $this->addLanguageFieldBasedOnContentListener,
            'onPreSetData',
        ]);
        $builder->addEventListener(FormEvents::PRE_SUBMIT, [
            $this->buildPathFromRootListener,
            'onPreSubmitData',
        ]);
    }
}
