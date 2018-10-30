<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\Content\Translation;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\Language;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Translation\TranslationAddData;
use EzSystems\EzPlatformAdminUi\Form\Type\Content\LocationType;
use EzSystems\EzPlatformAdminUi\Util\PermissionUtil;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use eZ\Publish\API\Repository\PermissionResolver;

class TranslationAddType extends AbstractType
{
    /** @var \eZ\Publish\API\Repository\LanguageService */
    protected $languageService;

    /** @var \eZ\Publish\API\Repository\ContentService */
    protected $contentService;

    /** @var \eZ\Publish\API\Repository\LocationService */
    protected $locationService;

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \EzSystems\EzPlatformAdminUi\Util\PermissionUtil */
    private $permissionUtil;

    /**
     * @param \eZ\Publish\API\Repository\LanguageService $languageService
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \eZ\Publish\API\Repository\LocationService $locationService
     * @param \eZ\Publish\API\Repository\PermissionResolver $permissionResolver
     * @param \EzSystems\EzPlatformAdminUi\Util\PermissionUtil $permissionUtil
     */
    public function __construct(
        LanguageService $languageService,
        ContentService $contentService,
        LocationService $locationService,
        PermissionResolver $permissionResolver,
        PermissionUtil $permissionUtil
    ) {
        $this->languageService = $languageService;
        $this->contentService = $contentService;
        $this->locationService = $locationService;
        $this->permissionResolver = $permissionResolver;
        $this->permissionUtil = $permissionUtil;
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
                'add',
                SubmitType::class,
                [
                    'label' => /** @Desc("Create") */ 'content_translation_add_form.add',
                ]
            )
            ->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'onPreSetData'])
            ->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'onPreSubmit']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TranslationAddData::class,
            'translation_domain' => 'forms',
        ]);
    }

    /**
     * Adds language fields and populates options list based on default form data.
     *
     * @param FormEvent $event
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function onPreSetData(FormEvent $event): void
    {
        $contentLanguages = [];
        $form = $event->getForm();
        $data = $event->getData();
        /** @var \eZ\Publish\API\Repository\Values\Content\Location $location */
        $location = $data->getLocation();

        if (null !== $location) {
            $contentInfo = $location->getContentInfo();
            $versionInfo = $this->contentService->loadVersionInfo($contentInfo);
            $contentLanguages = $versionInfo->languageCodes;
        }

        $this->addLanguageFields($form, $contentLanguages);
    }

    /**
     * Adds language fields and populates options list based on submitted form data.
     *
     * @param FormEvent $event
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function onPreSubmit(FormEvent $event): void
    {
        $contentLanguages = [];
        $form = $event->getForm();
        $data = $event->getData();

        if (isset($data['location'])) {
            try {
                $location = $this->locationService->loadLocation($data['location']);
            } catch (NotFoundException $e) {
                $location = null;
            }

            if (null !== $location) {
                $contentInfo = $location->getContentInfo();
                $versionInfo = $this->contentService->loadVersionInfo($contentInfo);
                $contentLanguages = $versionInfo->languageCodes;
            }
        }

        $this->addLanguageFields($form, $contentLanguages);
    }

    /**
     * Loads system languages with filtering applied.
     *
     * @param callable $filter
     *
     * @return array
     */
    public function loadLanguages(callable $filter): array
    {
        return array_filter(
            $this->languageService->loadLanguages(),
            $filter
        );
    }

    /**
     * Adds language fields to the $form. Language options are composed based on content language.
     *
     * @param FormInterface $form
     * @param string[] $contentLanguages
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function addLanguageFields(FormInterface $form, array $contentLanguages): void
    {
        $limitationLanguageCodes = [];

        $hasAccess = $this->permissionResolver->hasAccess('content', 'translate');
        if (is_array($hasAccess)) {
            $limitationLanguageCodes = $this->permissionUtil->getLimitationLanguageCodes($hasAccess);
        }

        $form
            ->add(
                'language',
                ChoiceType::class,
                [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => true,
                    'choice_loader' => new CallbackChoiceLoader(function () use ($contentLanguages, $limitationLanguageCodes) {
                        return $this->loadLanguages(
                            function (Language $language) use ($contentLanguages, $limitationLanguageCodes) {
                                return $language->enabled
                                    && !in_array($language->languageCode, $contentLanguages, true)
                                    && (empty($limitationLanguageCodes) || in_array($language->languageCode, $limitationLanguageCodes, true));
                            }
                        );
                    }),
                    'choice_value' => 'languageCode',
                    'choice_label' => 'name',
                ]
            )
            ->add(
                'base_language',
                ChoiceType::class,
                [
                    'required' => false,
                    'placeholder' => false,
                    'multiple' => false,
                    'expanded' => true,
                    'choice_loader' => new CallbackChoiceLoader(function () use ($contentLanguages) {
                        return $this->loadLanguages(
                            function (Language $language) use ($contentLanguages) {
                                return $language->enabled && in_array($language->languageCode, $contentLanguages, true);
                            }
                        );
                    }),
                    'choice_value' => 'languageCode',
                    'choice_label' => 'name',
                ]
            );
    }
}
