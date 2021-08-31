<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\ContentType\Translation;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\LanguageService;
use EzSystems\EzPlatformAdminUi\Form\Data\ContentType\Translation\TranslationAddData;
use EzSystems\EzPlatformAdminUi\Form\Type\ChoiceList\Loader\AvailableTranslationLanguageChoiceLoader;
use EzSystems\EzPlatformAdminUi\Form\Type\ChoiceList\Loader\BaseTranslationLanguageChoiceLoader;
use EzSystems\EzPlatformAdminUi\Form\Type\Content\ContentTypeType;
use EzSystems\EzPlatformAdminUi\Form\Type\ContentTypeGroup\ContentTypeGroupType;
use Symfony\Component\Form\AbstractType;
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

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /**
     * @param \eZ\Publish\API\Repository\LanguageService $languageService
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     */
    public function __construct(
        LanguageService $languageService,
        ContentTypeService $contentTypeService
    ) {
        $this->languageService = $languageService;
        $this->contentTypeService = $contentTypeService;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'contentType',
                ContentTypeType::class,
                [
                    'label' => false,
                    'attr' => [
                        'hidden' => true,
                    ],
                ]
            )
            ->add(
                'contentTypeGroup',
                ContentTypeGroupType::class
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

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
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
        $contentLanguages = [];
        $form = $event->getForm();

        /** @var \EzSystems\EzPlatformAdminUi\Form\Data\ContentType\Translation\TranslationAddData $data */
        $data = $event->getData();
        $contentType = $data->getContentType();

        if (null !== $contentType) {
            $contentLanguages = array_keys($contentType->getNames());
        }

        $this->addLanguageFields($form, $contentLanguages);
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
        $contentLanguages = [];
        $form = $event->getForm();
        $data = $event->getData();

        if (isset($data['contentType'])) {
            try {
                $contentType = $this->contentTypeService->loadContentTypeByIdentifier($data['contentType']);
            } catch (NotFoundException $e) {
                $contentType = null;
            }

            if (null !== $contentType) {
                $contentLanguages = array_keys($contentType->getNames());
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
     * @param \Symfony\Component\Form\FormInterface $form
     * @param string[] $contentLanguages
     *
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
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
                    'expanded' => false,
                    'choice_loader' => new AvailableTranslationLanguageChoiceLoader($this->languageService, $contentLanguages),
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
                    'choice_loader' => new BaseTranslationLanguageChoiceLoader($this->languageService, $contentLanguages),
                    'choice_value' => 'languageCode',
                    'choice_label' => 'name',
                ]
            );
    }
}
