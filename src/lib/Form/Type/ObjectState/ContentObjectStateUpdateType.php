<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\ObjectState;

use eZ\Publish\API\Repository\ObjectStateService;
use EzSystems\EzPlatformAdminUi\Form\Data\ObjectState\ContentObjectStateUpdateData;
use EzSystems\EzPlatformAdminUi\Form\Type\Content\ContentInfoType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentObjectStateUpdateType extends AbstractType
{
    /** @var ObjectStateService */
    protected $objectStateService;

    /**
     * @param ObjectStateService $objectStateService
     */
    public function __construct(ObjectStateService $objectStateService)
    {
        $this->objectStateService = $objectStateService;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('contentInfo', ContentInfoType::class, [
                'label' => false,
            ])
            ->add('objectStateGroup', ObjectStateGroupType::class, [
                'label' => false,
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var ContentObjectStateUpdateData $contentObjectStateUpdateData */
            $contentObjectStateUpdateData = $event->getData();
            $objectStateGroup = $contentObjectStateUpdateData->getObjectStateGroup();
            $form = $event->getForm();

            $form->add('objectState', ObjectStateChoiceType::class, [
                'label' => false,
                'choice_loader' => new CallbackChoiceLoader(function () use ($objectStateGroup) {
                    return $this->objectStateService->loadObjectStates($objectStateGroup);
                }),
            ]);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ContentObjectStateUpdateData::class,
            'translation_domain' => 'object_state',
        ]);
    }
}
