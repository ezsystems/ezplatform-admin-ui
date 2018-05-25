<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab\Dashboard;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use EzSystems\EzPlatformAdminUi\Tab\AbstractTab;
use EzSystems\EzPlatformAdminUi\Tab\ConditionalTabInterface;
use EzSystems\EzPlatformAdminUi\Tab\OrderedTabInterface;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;

class MyDraftsTab extends AbstractTab implements OrderedTabInterface, ConditionalTabInterface
{
    /** @var \eZ\Publish\API\Repository\ContentService */
    protected $contentService;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    protected $contentTypeService;

    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    protected $permissionResolver;

    /**
     * @param \Twig\Environment $twig
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     * @param \eZ\Publish\API\Repository\LocationService $locationService
     * @param \eZ\Publish\API\Repository\PermissionResolver $permissionResolver
     */
    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        ContentService $contentService,
        ContentTypeService $contentTypeService,
        LocationService $locationService,
        PermissionResolver $permissionResolver
    ) {
        parent::__construct($twig, $translator);

        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;
        $this->locationService = $locationService;
        $this->permissionResolver = $permissionResolver;
    }

    public function getIdentifier(): string
    {
        return 'my-drafts';
    }

    public function getName(): string
    {
        return /** @Desc("Drafts") */
            $this->translator->trans('tab.name.my_drafts', [], 'dashboard');
    }

    public function getOrder(): int
    {
        return 100;
    }

    /**
     * Get information about tab presence.
     *
     * @param array $parameters
     *
     * @return bool
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function evaluate(array $parameters): bool
    {
        // hide tab if user has absolutely no access to content/versionread
        return false !== $this->permissionResolver->hasAccess('content', 'versionread');
    }

    /**
     * @param array $parameters
     *
     * @return string
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function renderView(array $parameters): string
    {
        /** @todo Handle pagination */
        $page = 1;
        $limit = 10;
        $drafts = [];

        // if user has no access content/versionread for one of versions, exception is caught and draft array is empty
        try {
            $drafts = $this->contentService->loadContentDrafts();
        } catch (UnauthorizedException $e) {
        }

        // ContentService::loadContentDrafts returns unsorted list of VersionInfo.
        // Sort results by modification date, descending.
        usort($drafts, function (VersionInfo $a, VersionInfo $b) {
            return $b->modificationDate <=> $a->modificationDate;
        });

        $pager = new Pagerfanta(
            new ArrayAdapter($drafts)
        );

        $pager->setMaxPerPage($limit);
        $pager->setCurrentPage($page);

        $data = [];
        /** @var \eZ\Publish\API\Repository\Values\Content\VersionInfo $version */
        foreach ($pager as $version) {
            $contentInfo = $version->getContentInfo();
            $contentType = $this->contentTypeService->loadContentType($contentInfo->contentTypeId);

            if (null === $contentInfo->mainLocationId) {
                $locations = $this->locationService->loadParentLocationsForDraftContent($version);
                // empty Locations here means Location has been trashed and Draft should be ignored
                if (empty($locations)) {
                    continue;
                }
            }

            $data[] = [
                'contentId' => $contentInfo->id,
                'name' => $version->getName(),
                'type' => $contentType->getName(),
                'language' => $version->initialLanguageCode,
                'version' => $version->versionNo,
                'modified' => $version->modificationDate,
            ];
        }

        return $this->twig->render('EzPlatformAdminUiBundle:dashboard/tab:my_drafts.html.twig', [
            'data' => $data,
        ]);
    }
}
