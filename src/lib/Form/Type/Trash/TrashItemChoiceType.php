<?php

namespace EzPlatformAdminUi\Form\Type\Trash;

use EzPlatformAdminUi\Form\Data\TrashItemData;
use EzPlatformAdminUi\Service\TrashService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrashItemChoiceType extends AbstractType
{
    /**
     * @var TrashService
     */
    private $trashService;

    /**
     * @param TrashService $trashService
     */
    public function __construct(TrashService $trashService)
    {
        $this->trashService = $trashService;
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function getParent()
    {
        return ChoiceType::class;
    }
}
