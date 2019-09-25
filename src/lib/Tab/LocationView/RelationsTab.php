<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab\LocationView;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\Values\Content\Content;
use EzSystems\EzPlatformAdminUi\Tab\ConditionalTabInterface;
use EzSystems\EzPlatformAdminUi\Tab\AbstractEventDispatchingTab;
use EzSystems\EzPlatformAdminUi\Tab\OrderedTabInterface;
use EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class RelationsTab extends AbstractEventDispatchingTab implements OrderedTabInterface, ConditionalTabInterface
{
    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    protected $permissionResolver;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory */
    protected $datasetFactory;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    protected $contentTypeService;

    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        int $order,
        PermissionResolver $permissionResolver,
        DatasetFactory $datasetFactory,
        ContentTypeService $contentTypeService,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct($twig, $translator, $order, $eventDispatcher);

        $this->permissionResolver = $permissionResolver;
        $this->datasetFactory = $datasetFactory;
        $this->contentTypeService = $contentTypeService;
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

    public function getTemplate(): string
    {
        return '@ezdesign/content/tab/relations/tab.html.twig';
    }

    public function getTemplateParameters(array $contextParameters = []): array
    {
        /** @var Content $content */
        $content = $contextParameters['content'];
        $relationsDataset = $this->datasetFactory->relations();
        $relationsDataset->load($content);

        $contentTypeIds = [];

        $relations = $relationsDataset->getRelations();

        $viewParameters = [];

        foreach ($relations as $relation) {
            $contentTypeIds[] = $relation->getDestinationContentInfo()->contentTypeId;
        }

        $viewParameters['relations'] = $relations;

        if (true === $this->permissionResolver->hasAccess('content', 'reverserelatedlist')) {
            $reverseRelations = $relationsDataset->getReverseRelations();

            foreach ($reverseRelations as $relation) {
                $contentTypeIds[] = $relation->getSourceContentInfo()->contentTypeId;
            }

            $viewParameters['reverse_relations'] = $reverseRelations;
        }

        if (!empty($contentTypeIds)) {
            $viewParameters['content_types'] = $this->contentTypeService->loadContentTypeList(array_unique($contentTypeIds));
        } else {
            $viewParameters['content_types'] = [];
        }

        return array_replace($contextParameters, $viewParameters);
    }
}
