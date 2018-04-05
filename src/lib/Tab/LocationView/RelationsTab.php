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
use EzSystems\EzPlatformAdminUi\Tab\AbstractTab;
use EzSystems\EzPlatformAdminUi\Tab\OrderedTabInterface;
use EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;

class RelationsTab extends AbstractTab implements OrderedTabInterface
{
    /** @var PermissionResolver */
    protected $permissionResolver;

    /** @var DatasetFactory */
    protected $datasetFactory;

    /** @var ContentTypeService */
    protected $contentTypeService;

    /**
     * @param Environment $twig
     * @param TranslatorInterface $translator
     * @param PermissionResolver $permissionResolver
     * @param DatasetFactory $datasetFactory
     */
    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        PermissionResolver $permissionResolver,
        DatasetFactory $datasetFactory,
        ContentTypeService $contentTypeService
    ) {
        parent::__construct($twig, $translator);

        $this->permissionResolver = $permissionResolver;
        $this->datasetFactory = $datasetFactory;
        $this->contentTypeService = $contentTypeService;
    }

    public function getIdentifier(): string
    {
        return 'relations';
    }

    public function getName(): string
    {
        /** @Desc("Relations") */
        return $this->translator->trans('tab.name.relations', [], 'locationview');
    }

    public function getOrder(): int
    {
        return 500;
    }

    public function renderView(array $parameters): string
    {
        /** @var Content $content */
        $content = $parameters['content'];
        $versionInfo = $content->getVersionInfo();
        $relationsDataset = $this->datasetFactory->relations();
        $relationsDataset->load($versionInfo);

        $contentTypes = [];

        $relations = $relationsDataset->getRelations();

        $viewParameters = [];

        foreach ($relations as $relation) {
            $contentTypeId = $relation->getDestinationContentInfo()->contentTypeId;

            if (!isset($contentTypes[$contentTypeId])) {
                $contentTypes[$contentTypeId] = $this->contentTypeService->loadContentType($contentTypeId);
            }
        }

        $viewParameters['relations'] = $relations;

        if (true === $this->permissionResolver->hasAccess('content', 'reverserelatedlist')) {
            $reverseRelations = $relationsDataset->getReverseRelations();

            foreach ($reverseRelations as $relation) {
                $contentTypeId = $relation->getSourceContentInfo()->contentTypeId;

                if (!isset($contentTypes[$contentTypeId])) {
                    $contentTypes[$contentTypeId] = $this->contentTypeService->loadContentType($contentTypeId);
                }
            }

            $viewParameters['reverse_relations'] = $reverseRelations;
        }

        $viewParameters['contentTypes'] = $contentTypes;

        return $this->twig->render(
            'EzPlatformAdminUiBundle:content/tab/relations:tab.html.twig',
            array_merge($viewParameters, $parameters)
        );
    }
}
