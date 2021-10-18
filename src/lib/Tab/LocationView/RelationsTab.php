<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab\LocationView;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\PermissionResolver;
use EzSystems\EzPlatformAdminUi\Pagination\Pagerfanta\ReverseRelationAdapter;
use EzSystems\EzPlatformAdminUi\Tab\AbstractEventDispatchingTab;
use EzSystems\EzPlatformAdminUi\Tab\ConditionalTabInterface;
use EzSystems\EzPlatformAdminUi\Tab\OrderedTabInterface;
use EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory;
use Pagerfanta\Pagerfanta;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class RelationsTab extends AbstractEventDispatchingTab implements OrderedTabInterface, ConditionalTabInterface
{
    public const URI_FRAGMENT = 'ibexa-tab-location-view-relations';

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    protected $permissionResolver;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory */
    protected $datasetFactory;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    protected $contentTypeService;

    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /**
     * @param \Twig\Environment $twig
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator
     * @param \eZ\Publish\API\Repository\PermissionResolver $permissionResolver
     * @param \EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory $datasetFactory
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     */
    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        PermissionResolver $permissionResolver,
        DatasetFactory $datasetFactory,
        ContentTypeService $contentTypeService,
        EventDispatcherInterface $eventDispatcher,
        ContentService $contentService
    ) {
        parent::__construct($twig, $translator, $eventDispatcher);

        $this->permissionResolver = $permissionResolver;
        $this->datasetFactory = $datasetFactory;
        $this->contentTypeService = $contentTypeService;
        $this->contentService = $contentService;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'relations';
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        /** @Desc("Relations") */
        return $this->translator->trans('tab.name.relations', [], 'locationview');
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return 500;
    }

    /**
     * Get information about tab presence.
     *
     * @param array $parameters
     *
     * @return bool
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function evaluate(array $parameters): bool
    {
        return $this->permissionResolver->canUser('content', 'reverserelatedlist', $parameters['content']);
    }

    /**
     * @inheritdoc
     */
    public function getTemplate(): string
    {
        return '@ezdesign/content/tab/relations/tab.html.twig';
    }

    /**
     * @inheritdoc
     */
    public function getTemplateParameters(array $contextParameters = []): array
    {
        /** @var \eZ\Publish\API\Repository\Values\Content\Content $content */
        $content = $contextParameters['content'];
        $reverseRelationPaginationParams = $contextParameters['reverse_relation_pagination_params'];
        $reverseRelationPagination = new Pagerfanta(
            new ReverseRelationAdapter($this->contentService, $this->datasetFactory, $content)
        );
        $reverseRelationPagination->setMaxPerPage($reverseRelationPaginationParams['limit']);
        $reverseRelationPagination->setCurrentPage(min(
            max($reverseRelationPaginationParams['page'], 1),
            $reverseRelationPagination->getNbPages()
        ));

        $contentTypeIds = [];

        $relationListDataset = $this->datasetFactory->relationList();
        $relationListDataset->load($content);
        $relations = $relationListDataset->getRelations();

        $viewParameters = [];

        foreach ($relations as $relation) {
            if ($relation->isAccessible()) {
                /** @var \eZ\Publish\API\Repository\Values\Content\Relation $relation */
                $contentTypeIds[] = $relation->getDestinationContentInfo()->contentTypeId;
            }
        }

        $viewParameters['relations'] = $relations;

        if ($this->permissionResolver->canUser('content', 'reverserelatedlist', $content)) {
            $reverseRelations = $reverseRelationPagination->getCurrentPageResults();

            foreach ($reverseRelations as $relation) {
                if ($relation->isAccessible()) {
                    /** @var \eZ\Publish\API\Repository\Values\Content\Relation $relation */
                    $contentTypeIds[] = $relation->getSourceContentInfo()->contentTypeId;
                }
            }

            $viewParameters['reverse_relation_pager'] = $reverseRelationPagination;
            $viewParameters['reverse_relation_pagination_params'] = $reverseRelationPaginationParams;
        }

        if (!empty($contentTypeIds)) {
            $viewParameters['content_types'] = $this->contentTypeService->loadContentTypeList(array_unique($contentTypeIds));
        } else {
            $viewParameters['content_types'] = [];
        }

        return array_replace($contextParameters, $viewParameters);
    }
}
