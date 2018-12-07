<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\ContentType;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\API\Repository\LanguageService;
use EzSystems\EzPlatformAdminUi\Form\Data\ContentType\ContentTypeEditData;
use EzSystems\EzPlatformAdminUi\Form\Type\Content\ContentTypeType;
use EzSystems\EzPlatformAdminUi\Form\Type\ContentTypeGroup\ContentTypeGroupType;
use EzSystems\EzPlatformAdminUi\Form\Type\Language\LanguageChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Exception\AlreadySubmittedException;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentTypeEditType extends AbstractType
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
                'content_type',
                ContentTypeType::class,
                [
                    'label' => false,
                    'attr' => [
                        'hidden' => true,
                    ],
                ]
            )
            ->add(
                'content_type_group',
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
            'data_class' => ContentTypeEditData::class,
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

        /** @var ContentTypeEditData $data */
        $data = $event->getData();
        $contentType = $data->getContentType();

        if (null !== $contentType) {
            $contentLanguages = $contentType->languageCodes;
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

        if (isset($data['content_type'])) {
            try {
                $contentType = $this->contentTypeService->loadContentTypeByIdentifier($data['content_type']);
            } catch (NotFoundException $e) {
                $contentType = null;
            }

            if (null !== $contentType) {
                $contentLanguages = $contentType->languageCodes;
            }
        }

        $this->addLanguageFields($form, $contentLanguages);
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
                LanguageChoiceType::class,
                [
                    'required' => true,
                    'label' => false,
                    'multiple' => false,
                    'expanded' => true,
                    'choice_loader' => new CallbackChoiceLoader(function () use ($contentLanguages) {
                        return array_map([$this->languageService, 'loadLanguage'], $contentLanguages);
                    }),
                ]
            );
    }
}
