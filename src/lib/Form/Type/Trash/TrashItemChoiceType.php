<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\Type\Trash;

use eZ\Publish\API\Repository\TrashService;
use eZ\Publish\API\Repository\Values\Content\Query;
use EzSystems\EzPlatformAdminUi\Form\Data\TrashItemData;
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
            'choices' => $this->trashService->findTrashItems(new Query([
                'sortClauses' => [new Query\SortClause\Location\Priority(Query::SORT_ASC)],
            ])),
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
    public function getParent(): ?string
    {
        return ChoiceType::class;
    }
}
