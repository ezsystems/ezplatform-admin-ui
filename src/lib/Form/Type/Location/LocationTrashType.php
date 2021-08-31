<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\Location;

use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationTrashData;
use EzSystems\EzPlatformAdminUi\Form\TrashLocationOptionProvider\OptionsFactory;
use EzSystems\EzPlatformAdminUi\Form\Type\Content\LocationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class LocationTrashType extends AbstractType
{
    public const CONFIRM_SEND_TO_TRASH = 'confirm_send_to_trash';

    /** @var \EzSystems\EzPlatformAdminUi\Form\TrashLocationOptionProvider\OptionsFactory */
    private $trashTypeStrategy;

    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
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
            $form = $event->getForm();
            $this->trashTypeStrategy->addOptions(
                $form,
                $form->getParent()->getData()->getLocation()
            );

            if (!empty($form->getParent()->get('trash_options')->all())) {
                $this->addConfirmCheckbox($form->getParent());
            }
        });

        $builder->get('location')->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm()->getParent();
            $this->trashTypeStrategy->addOptions(
                $form->get('trash_options'),
                $event->getForm()->getData()
            );

            if (!empty($form->get('trash_options')->all())) {
                $this->addConfirmCheckbox($form);
            }
        });
    }

    protected function addConfirmCheckbox(FormInterface $form): void
    {
        $form->add(
            'confirm',
                ChoiceType::class,
            [
                'expanded' => true,
                'multiple' => true,
                'required' => true,
                'mapped' => false,
                'label' => false,
                'choices' => [
                    /** @Desc("I understand the consequences of this action.") */
                    $this->translator->trans('location_trash_form.confirm_label') => self::CONFIRM_SEND_TO_TRASH,
                ],
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => LocationTrashData::class,
            'translation_domain' => 'forms',
        ]);
    }
}
