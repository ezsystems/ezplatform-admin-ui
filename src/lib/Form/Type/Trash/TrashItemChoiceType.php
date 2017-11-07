<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\Type\Trash;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\TrashService;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\TrashItem;
use EzSystems\EzPlatformAdminUi\Form\Data\TrashItemData;
use EzSystems\EzPlatformAdminUi\UI\Service\PathService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrashItemChoiceType extends AbstractType
{
    /** @var TrashService */
    private $trashService;

    /** @var PathService */
    private $pathService;

    /** @var ContentTypeService */
    private $contentTypeService;

    /**
     * @param TrashService $trashService
     * @param PathService $pathService
     * @param ContentTypeService $contentTypeService
     */
    public function __construct(
        TrashService $trashService,
        PathService $pathService,
        ContentTypeService $contentTypeService
    ) {
        $this->trashService = $trashService;
        $this->pathService = $pathService;
        $this->contentTypeService = $contentTypeService;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => $this->getTrashItemDataChoices(),
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

    /**
     * @return array
     */
    private function getTrashItemDataChoices(): array
    {
        $trashItems = $this->trashService->findTrashItems(new Query([
            'sortClauses' => [new Query\SortClause\Location\Priority(Query::SORT_ASC)],
        ]))->items;

        return array_map(function (TrashItem $item) {
            $contentType = $this->contentTypeService->loadContentType($item->contentInfo->contentTypeId);
            $ancestors = $this->pathService->loadPathLocations($item);

            return new TrashItemData($item, $contentType, $ancestors);
        }, $trashItems);
    }
}
