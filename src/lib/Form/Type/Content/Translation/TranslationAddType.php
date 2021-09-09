<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\Content\Translation;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\User\Limitation;
use eZ\Publish\SPI\Limitation\Target;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Translation\TranslationAddData;
use EzSystems\EzPlatformAdminUi\Form\Type\Content\LocationType;
use EzSystems\EzPlatformAdminUi\Permission\LookupLimitationsTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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

    /** @var \EzSystems\EzPlatformAdminUi\Permission\LookupLimitationsTransformer */
    private $lookupLimitationsTransformer;

    /**
     * @param \eZ\Publish\API\Repository\LanguageService $langaugeService
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \eZ\Publish\API\Repository\LocationService $locationService
     * @param \eZ\Publish\API\Repository\PermissionResolver $permissionResolver
     * @param \EzSystems\EzPlatformAdminUi\Permission\LookupLimitationsTransformer $lookupLimitationsTransformer
     */
    public function __construct(
        LanguageService $langaugeService,
        ContentService $contentService,
        LocationService $locationService,
        PermissionResolver $permissionResolver,
        LookupLimitationsTransformer $lookupLimitationsTransformer
    ) {
        $this->languageService = $langaugeService;
        $this->contentService = $contentService;
        $this->locationService = $locationService;
        $this->permissionResolver = $permissionResolver;
        $this->lookupLimitationsTransformer = $lookupLimitationsTransformer;
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
     * @param \Symfony\Component\Form\FormEvent $event
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function onPreSetData(FormEvent $event)
    {
        $contentInfo = null;
        $contentLanguages = [];
        $form = $event->getForm();
        $data = $event->getData();
        $location = $data->getLocation();

        if (null !== $location) {
            $contentInfo = $location->getContentInfo();
            $versionInfo = $this->contentService->loadVersionInfo($contentInfo);
            $contentLanguages = $versionInfo->languageCodes;
        }

        $this->addLanguageFields($form, $contentLanguages, $contentInfo, $location);
    }

    /**
     * Adds language fields and populates options list based on submitted form data.
     *
     * @param \Symfony\Component\Form\FormEvent $event
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function onPreSubmit(FormEvent $event)
    {
        $contentInfo = null;
        $contentLanguages = [];
        $form = $event->getForm();
        $data = $event->getData();

        $location = null;
        if (isset($data['location'])) {
            try {
                $location = $this->locationService->loadLocation((int)$data['location']);
            } catch (NotFoundException $e) {
                $location = null;
            }

            if (null !== $location) {
                $contentInfo = $location->getContentInfo();
                $versionInfo = $this->contentService->loadVersionInfo($contentInfo);
                $contentLanguages = $versionInfo->languageCodes;
            }
        }

        $this->addLanguageFields($form, $contentLanguages, $contentInfo, $location);
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
     * @param \Symfony\Component\Form\FormInterface $form
     * @param string[] $contentLanguages
     * @param \eZ\Publish\API\Repository\Values\Content\ContentInfo|null $contentInfo
     * @param \eZ\Publish\API\Repository\Values\Content\Location|null $location
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function addLanguageFields(
        FormInterface $form,
        array $contentLanguages,
        ?ContentInfo $contentInfo,
        ?Location $location = null
    ): void {
        $languagesCodes = array_column($this->languageService->loadLanguages(), 'languageCode');

        $limitationLanguageCodes = [];
        if (null !== $contentInfo) {
            $lookupLimitations = $this->permissionResolver->lookupLimitations(
                'content',
                'edit',
                $contentInfo,
                [
                    (new Target\Builder\VersionBuilder())->translateToAnyLanguageOf($languagesCodes)->build(),
                    $this->locationService->loadLocation(
                        $location !== null
                            ? $location->id
                            : $contentInfo->mainLocationId
                    ),
                ],
                [Limitation::LANGUAGE]
            );

            $limitationLanguageCodes = $this->lookupLimitationsTransformer->getFlattenedLimitationsValues($lookupLimitations);
        }

        $form
            ->add(
                'language',
                ChoiceType::class,
                [
                    'required' => true,
                    'placeholder' => false,
                    'multiple' => false,
                    'expanded' => false,
                    'choice_loader' => new CallbackChoiceLoader(function () use ($contentLanguages, $limitationLanguageCodes) {
                        return $this->loadLanguages(
                            static function (Language $language) use ($contentLanguages, $limitationLanguageCodes) {
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
                    'expanded' => false,
                    'choice_loader' => new CallbackChoiceLoader(function () use ($contentLanguages) {
                        return $this->loadLanguages(
                            static function (Language $language) use ($contentLanguages) {
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
