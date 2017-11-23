<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\Content\Translation;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\Language;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Translation\TranslationAddData;
use EzSystems\EzPlatformAdminUi\Form\Type\Content\LocationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Exception\AlreadySubmittedException;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TranslationAddType extends AbstractType
{
    /** @var LanguageService */
    protected $languageService;

    /** @var ContentService */
    protected $contentService;

    /** @var LocationService */
    protected $locationService;

    /**
     * @param LanguageService $langaugeService
     * @param ContentService $contentService
     * @param LocationService $locationService
     */
    public function __construct(LanguageService $langaugeService, ContentService $contentService, LocationService $locationService)
    {
        $this->languageService = $langaugeService;
        $this->contentService = $contentService;
        $this->locationService = $locationService;
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
     * @throws UnauthorizedException
     * @throws NotFoundException
     * @throws AlreadySubmittedException
     * @throws LogicException
     * @throws UnexpectedTypeException
     */
    public function onPreSetData(FormEvent $event)
    {
        $contentLanguages = [];
        $form = $event->getForm();
        $data = $event->getData();
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
     * @throws UnauthorizedException
     * @throws NotFoundException
     * @throws AlreadySubmittedException
     * @throws LogicException
     * @throws UnexpectedTypeException
     */
    public function onPreSubmit(FormEvent $event)
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
     * @throws AlreadySubmittedException
     * @throws LogicException
     * @throws UnexpectedTypeException
     */
    public function addLanguageFields(FormInterface $form, array $contentLanguages): void
    {
        $form
            ->add(
                'language',
                ChoiceType::class,
                [
                    'required' => true,
                    'multiple' => false,
                    'expanded' => true,
                    'choice_loader' => new CallbackChoiceLoader(function () use ($contentLanguages) {
                        return $this->loadLanguages(
                            function (Language $language) use ($contentLanguages) {
                                return $language->enabled && !in_array($language->languageCode, $contentLanguages, true);
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
