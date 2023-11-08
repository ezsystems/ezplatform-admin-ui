<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\ObjectState;

use eZ\Publish\API\Repository\ObjectStateService;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\Values\ObjectState\ObjectState;
use EzSystems\EzPlatformAdminUi\Form\Data\ObjectState\ContentObjectStateUpdateData;
use EzSystems\EzPlatformAdminUi\Form\Type\Content\LocationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentObjectStateUpdateType extends AbstractType
{
    /** @var \eZ\Publish\API\Repository\ObjectStateService */
    protected $objectStateService;

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    private $permissionResolver;

    public function __construct(ObjectStateService $objectStateService, PermissionResolver $permissionResolver)
    {
        $this->objectStateService = $objectStateService;
        $this->permissionResolver = $permissionResolver;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('location', LocationType::class, [
                'label' => false,
            ])
            ->add('objectStateGroup', ObjectStateGroupType::class, [
                'label' => false,
            ])
            ->add('set', SubmitType::class, [
                'label' => /** @Desc("Set") */ 'object_state.button.set',
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var \EzSystems\EzPlatformAdminUi\Form\Data\ObjectState\ContentObjectStateUpdateData $contentObjectStateUpdateData */
            $contentObjectStateUpdateData = $event->getData();
            $objectStateGroup = $contentObjectStateUpdateData->getObjectStateGroup();
            $location = $contentObjectStateUpdateData->getLocation();
            $form = $event->getForm();

            $form->add('objectState', ObjectStateChoiceType::class, [
                'label' => false,
                'choice_loader' => new CallbackChoiceLoader(
                    function () use ($objectStateGroup, $location) {
                        return array_filter(
                            $this->objectStateService->loadObjectStates($objectStateGroup),
                            function (ObjectState $objectState) use ($location) {
                                return $this->permissionResolver->canUser(
                                    'state',
                                    'assign',
                                    $location->getContentInfo(),
                                    [$location, $objectState],
                                );
                            }
                        );
                    }),
            ]);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContentObjectStateUpdateData::class,
            'translation_domain' => 'object_state',
        ]);
    }
}
