<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\Location;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\LocationService;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationTrashContainerData;
use EzSystems\EzPlatformAdminUi\Form\Type\Content\LocationType;
use EzSystems\EzPlatformAdminUi\Specification\Location\HasChildren;
use EzSystems\EzPlatformAdminUi\Specification\Location\IsContainer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @deprecated since 2.5, to be removed in 3.0.
 */
class LocationTrashContainerType extends AbstractType
{
    const TRASH_WITH_CHILDREN = 'trash_with_children';

    /** @var \Symfony\Component\Translation\TranslatorInterface */
    private $translator;

    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /**
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     * @param \eZ\Publish\API\Repository\LocationService $locationService
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     */
    public function __construct(
        TranslatorInterface $translator,
        LocationService $locationService,
        ContentTypeService $contentTypeService
    ) {
        $this->translator = $translator;
        $this->locationService = $locationService;
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
                'trashContainer',
                ChoiceType::class,
                [
                    'expanded' => true,
                    'multiple' => true,
                ]
            )
            ->add(
                'trash',
                SubmitType::class,
                ['label' => /** @Desc("Send to Trash") */
                    'location_trash_form.trash', ]
            );

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $this->addTrashContainerField(
                $event->getForm(),
                $event->getData()->getLocation()
            );
        });

        $builder->get('location')->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $this->addTrashContainerField(
                $event->getForm()->getParent(),
                $event->getForm()->getData()
            );
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => LocationTrashContainerData::class,
            'translation_domain' => 'forms',
        ]);
    }

    /**
     * @param \Symfony\Component\Form\FormInterface $form
     * @param \eZ\Publish\API\Repository\Values\Content\Location|null $location
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentValue
     */
    private function addTrashContainerField(FormInterface $form, Location $location = null): void
    {
        if (null === $location) {
            return;
        }

        $isContainer = new IsContainer();
        $hasChildren = new HasChildren($this->locationService);

        if (!$isContainer->and($hasChildren)->isSatisfiedBy($location)) {
            return;
        }

        $locationChildren = $this->locationService->loadLocationChildren($location);

        $translatorParameters = [
            '%children_count%' => $locationChildren->totalCount,
            '%content_name%' => $location->getContent()->getName(),
        ];

        $form->add('trashContainer', ChoiceType::class, [
            'expanded' => true,
            'multiple' => true,
            'choices' => [
                /** @Desc("Send %children_count% Content item(s) of this Location to Trash") */
                $this->translator->trans('location_trash_form.trash_container', $translatorParameters, 'forms') => self::TRASH_WITH_CHILDREN,
            ],
        ]);
    }
}
