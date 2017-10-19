<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\Type\Trash;

use EzSystems\EzPlatformAdminUi\Form\Data\TrashItemData;
use EzSystems\EzPlatformAdminUi\Service\TrashService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrashItemChoiceType extends AbstractType
{
    /** @var TrashService */
    private $trashService;

    /**
     * @param TrashService $trashService
     */
    public function __construct(TrashService $trashService)
    {
        $this->trashService = $trashService;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => $this->trashService->loadTrashItems(),
            'choice_attr' => function (TrashItemData $val) {
                return [
                    'data-is-parent-in-trash' => (int)$val->isParentInTrash(),
                ];
            },
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }
}
