<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\Location;

use eZ\Publish\API\Repository\ContentTypeService;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationTrashWithAssetData;
use EzSystems\EzPlatformAdminUi\Form\Type\Content\LocationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

class LocationTrashWithAssetType extends AbstractType
{
    const RADIO_SELECT_TRASH_WITH_ASSETS = 'trash_with_assets';
    const RADIO_SELECT_DEFAULT_TRASH = 'trash_default';

    /** @var \Symfony\Component\Translation\TranslatorInterface */
    private $translator;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /**
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     */
    public function __construct(
        TranslatorInterface $translator,
        ContentTypeService $contentTypeService
    ) {
        $this->translator = $translator;
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
                'location',
                LocationType::class,
                ['label' => false]
            )
            ->add(
                'trashAssets',
                ChoiceType::class,
                [
                    'expanded' => true,
                    'multiple' => false,
                ]
            )
            ->add(
                'trash',
                SubmitType::class,
                ['label' => /** @Desc("Send to Trash") */ 'location_trash_form.trash']
            );

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $formEvent) {
            /** @var \EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationTrashWithAssetData $data */
            $data = $formEvent->getData();
            $form = $formEvent->getForm();

            $location = $data->getLocation();
            $contentName = $location->getContent()->getName();

            $contentType = $this->contentTypeService
                ->loadContentType(
                    $location->getContentInfo()->contentTypeId)
                ->getName(
                    $location->getContentInfo()->mainLanguageCode
                );

            $translatorParameters = ['%content_name%' => $contentName, '%content_type%' => $contentType];
            $form->add(
                'trashAssets',
                ChoiceType::class,
                [
                    'expanded' => true,
                    'multiple' => false,
                    'choices' => [
                        /** @Desc("Delete only %content_name% (%content_type%)") */
                        $this->translator->trans('location_trash_form.default_trash', $translatorParameters, 'forms') => LocationTrashWithAssetType::RADIO_SELECT_DEFAULT_TRASH,
                        /** @Desc("Delete %content_name% (%content_type%) and its related image assets") */
                        $this->translator->trans('location_trash_form.trash_with_asset', $translatorParameters, 'forms') => LocationTrashWithAssetType::RADIO_SELECT_TRASH_WITH_ASSETS,
                    ],
                ]
            );
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => LocationTrashWithAssetData::class,
            'translation_domain' => 'forms',
        ]);
    }
}
