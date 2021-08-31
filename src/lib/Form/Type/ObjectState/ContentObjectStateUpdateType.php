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
use EzSystems\EzPlatformAdminUi\Form\Type\Content\ContentInfoType;
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

    /**
     * @param \eZ\Publish\API\Repository\ObjectStateService $objectStateService
     * @param \eZ\Publish\API\Repository\PermissionResolver $permissionResolver
     */
    public function __construct(ObjectStateService $objectStateService, PermissionResolver $permissionResolver)
    {
        $this->objectStateService = $objectStateService;
        $this->permissionResolver = $permissionResolver;
    }

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('contentInfo', ContentInfoType::class, [
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
            $contentInfo = $contentObjectStateUpdateData->getContentInfo();
            $form = $event->getForm();

            $form->add('objectState', ObjectStateChoiceType::class, [
                'label' => false,
                'choice_loader' => new CallbackChoiceLoader(function () use ($objectStateGroup, $contentInfo) {
                    $contentState = $this->objectStateService->getContentState($contentInfo, $objectStateGroup);

                    return array_filter(
                        $this->objectStateService->loadObjectStates($objectStateGroup),
                        function (ObjectState $objectState) use ($contentInfo, $contentState) {
                            return $this->permissionResolver->canUser('state', 'assign', $contentInfo, [$objectState]);
                        }
                    );
                }),
            ]);
        });
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ContentObjectStateUpdateData::class,
            'translation_domain' => 'object_state',
        ]);
    }
}
