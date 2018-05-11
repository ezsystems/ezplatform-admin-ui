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
use EzSystems\EzPlatformAdminUi\Tab\ConditionalTabInterface;
use EzSystems\EzPlatformAdminUi\Tab\OrderedTabInterface;
use EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;

class RelationsTab extends AbstractTab implements OrderedTabInterface, ConditionalTabInterface
{
    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    protected $permissionResolver;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory */
    protected $datasetFactory;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    protected $contentTypeService;

    /**
     * @param \Twig\Environment $twig
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     * @param \eZ\Publish\API\Repository\PermissionResolver $permissionResolver
     * @param \EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory $datasetFactory
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
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
     * @param array $parameters
     *
     * @return string
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
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
            '@ezdesign/content/tab/relations/tab.html.twig',
            array_merge($viewParameters, $parameters)
        );
    }
}
