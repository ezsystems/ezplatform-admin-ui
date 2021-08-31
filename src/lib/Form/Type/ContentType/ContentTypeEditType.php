<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\ContentType;

use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use EzSystems\EzPlatformAdminUi\Form\Data\ContentType\ContentTypeEditData;
use EzSystems\EzPlatformAdminUi\Form\Type\Content\ContentTypeType;
use EzSystems\EzPlatformAdminUi\Form\Type\ContentTypeGroup\ContentTypeGroupType;
use EzSystems\EzPlatformAdminUi\Form\Type\Language\LanguageChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentTypeEditType extends AbstractType
{
    /** @var \eZ\Publish\API\Repository\LanguageService */
    protected $languageService;

    public function __construct(
        LanguageService $languageService
    ) {
        $this->languageService = $languageService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var \eZ\Publish\API\Repository\Values\ContentType\ContentType $contentType */
        $contentType = $options['contentType'];
        $contentTypeLanguages = $contentType->languageCodes;

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
                'language',
                LanguageChoiceType::class,
                [
                    'required' => true,
                    'label' => false,
                    'multiple' => false,
                    'expanded' => true,
                    'choice_loader' => new CallbackChoiceLoader(function () use ($contentTypeLanguages) {
                        return array_map([$this->languageService, 'loadLanguage'], $contentTypeLanguages);
                    }),
                ]
            )
            ->add(
                'add',
                SubmitType::class,
                [
                    'attr' => ['hidden' => true],
                    'label' => /** @Desc("Create") */ 'content_translation_add_form.add',
                ]
            );
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => ContentTypeEditData::class,
                'translation_domain' => 'forms',
            ])
            ->setRequired('contentType')
            ->setAllowedTypes('contentType', ContentType::class);
    }
}
