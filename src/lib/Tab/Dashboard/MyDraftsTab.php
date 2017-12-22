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
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use EzSystems\EzPlatformAdminUi\Tab\AbstractTab;
use EzSystems\EzPlatformAdminUi\Tab\OrderedTabInterface;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;

class MyDraftsTab extends AbstractTab implements OrderedTabInterface
{
    /** @var ContentService */
    protected $contentService;

    /** @var ContentTypeService */
    protected $contentTypeService;

    /** @var LocationService */
    private $locationService;

    /**
     * @param Environment $twig
     * @param TranslatorInterface $translator
     * @param ContentService $contentService
     * @param ContentTypeService $contentTypeService
     * @param LocationService $locationService
     */
    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        ContentService $contentService,
        ContentTypeService $contentTypeService,
        LocationService $locationService
    ) {
        parent::__construct($twig, $translator);

        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;
        $this->locationService = $locationService;
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

    public function renderView(array $parameters): string
    {
        /** @todo Handle pagination */
        $page = 1;
        $limit = 10;

        $drafts = $this->contentService->loadContentDrafts();

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
        /** @var VersionInfo $version */
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
