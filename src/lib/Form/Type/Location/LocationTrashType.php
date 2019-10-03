<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\Location;

use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationTrashData;
use EzSystems\EzPlatformAdminUi\Form\TrashLocationStrategy\OptionsFactory;
use EzSystems\EzPlatformAdminUi\Form\Type\Content\LocationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

class LocationTrashType extends AbstractType
{
    /** @var \EzSystems\EzPlatformAdminUi\Form\TrashLocationStrategy\OptionsFactory */
    private $trashTypeStrategy;

    /** @var \Symfony\Component\Translation\TranslatorInterface */
    private $translator;

    public function __construct(
        OptionsFactory $trashTypeStrategy,
        TranslatorInterface $translator
    ) {
        $this->trashTypeStrategy = $trashTypeStrategy;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'location',
                LocationType::class,
                ['label' => false]
            )
            ->add('trash_options', CollectionType::class, [
                'entry_type' => ChoiceType::class,
                'allow_add' => true,
                'label' => false,
            ])
            ->add(
                'trash',
                SubmitType::class,
                ['label' => /** @Desc("Send to Trash") */ 'location_trash_form.trash']
            );

        $builder->get('trash_options')->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $this->trashTypeStrategy->addOptions(
                $event->getForm(),
                $event->getForm()->getParent()->getData()->getLocation()
            );
        });

        $builder->get('location')->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $this->trashTypeStrategy->addOptions(
                $event->getForm()->getParent()->get('trash_options'),
                $event->getForm()->getData()
            );
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => LocationTrashData::class,
            'translation_domain' => 'forms',
        ]);
    }
}
