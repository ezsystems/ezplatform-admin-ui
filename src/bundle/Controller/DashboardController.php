<?php

namespace EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use eZ\Publish\Core\Pagination\Pagerfanta\ContentSearchAdapter;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

class DashboardController extends Controller
{
    private $permissionResolver;

    private $searchService;

    private $contentService;

    private $contentTypeService;

    private $userService;

    public function __construct(
        PermissionResolver $permissionResolver,
        SearchService $searchService,
        ContentService $contentService,
        ContentTypeService $contentTypeService,
        UserService $userService)
    {
        $this->permissionResolver = $permissionResolver;
        $this->searchService = $searchService;
        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;
        $this->userService = $userService;
    }

    public function dashboardAction()
    {
        return $this->render('@EzPlatformAdminUi/dashboard/dashboard.html.twig');
    }

    public function myDraftsAction($page = 1, $limit = 10)
    {
        $pager = new Pagerfanta(
            new ArrayAdapter(
                $this->contentService->loadContentDrafts()
            )
        );
        $pager->setMaxPerPage($limit);
        $pager->setCurrentPage($page);

        $data = [];
        /** @var VersionInfo $version */
        foreach ($pager as $version) {
            $contentInfo = $version->getContentInfo();
            $contentType = $this->contentTypeService->loadContentType($contentInfo->contentTypeId);

            $data[] = [
                'contentId' => $contentInfo->id,
                'name' => $version->getName(),
                'type' => $contentType->getName(),
                'language' => $version->initialLanguageCode,
                'version' => $version->versionNo,
                'modified' => $version->modificationDate,
            ];
        }

        return $this->render('@EzPlatformAdminUi/dashboard/tab/my_drafts.html.twig', [
            'data' => $data,
        ]);
    }

    public function myContentAction($page = 1, $limit = 10)
    {
        $query = $this->getSubtreeQuery(
            '/1/2/',
            $this->permissionResolver->getCurrentUserReference()->getUserId()
        );

        $pager = new Pagerfanta(
            new ContentSearchAdapter($query, $this->searchService)
        );
        $pager->setMaxPerPage($limit);
        $pager->setCurrentPage($page);

        return $this->render('@EzPlatformAdminUi/dashboard/tab/my_content.html.twig', [
            'data' => $this->mapPagerContentToData($pager),
        ]);
    }

    public function myMediaAction($page = 1, $limit = 10)
    {
        $query = $this->getSubtreeQuery(
            '/1/43/',
            $this->permissionResolver->getCurrentUserReference()->getUserId()
        );

        $pager = new Pagerfanta(
            new ContentSearchAdapter($query, $this->searchService)
        );
        $pager->setMaxPerPage($limit);
        $pager->setCurrentPage($page);

        return $this->render('@EzPlatformAdminUi/dashboard/tab/my_media.html.twig', [
            'data' => $this->mapPagerContentToData($pager),
        ]);
    }

    public function allContentAction($page = 1, $limit = 10)
    {
        $pager = new Pagerfanta(
            new ContentSearchAdapter(
                $this->getSubtreeQuery('/1/2/'),
                $this->searchService
            )
        );
        $pager->setMaxPerPage($limit);
        $pager->setCurrentPage($page);

        return $this->render('@EzPlatformAdminUi/dashboard/tab/all_content.html.twig', [
            'data' => $this->mapPagerContentToData($pager),
        ]);
    }

    public function allMediaAction($page = 1, $limit = 10)
    {
        $pager = new Pagerfanta(
            new ContentSearchAdapter(
                $this->getSubtreeQuery('/1/43/'),
                $this->searchService
            )
        );
        $pager->setMaxPerPage($limit);
        $pager->setCurrentPage($page);

        return $this->render('@EzPlatformAdminUi/dashboard/tab/all_media.html.twig', [
            'data' => $this->mapPagerContentToData($pager),
        ]);
    }

    private function getSubtreeQuery(string $subtree, $ownerId = false): Query
    {
        $query = new Query();
        $subtreeCriterion = new Query\Criterion\Subtree($subtree);

        if ($ownerId) {
            $query->filter = new Query\Criterion\LogicalAnd([
                $subtreeCriterion,
                new Query\Criterion\UserMetadata(
                    Query\Criterion\UserMetadata::OWNER,
                    Query\Criterion\Operator::EQ,
                    $ownerId
                ),
            ]);
        }
        else {
            $query->filter = $subtreeCriterion;
        }

        $query->sortClauses = [
            new Query\SortClause\DateModified(Query::SORT_DESC),
        ];

        return $query;
    }

    private function mapPagerContentToData(Pagerfanta $pager)
    {
        $data = [];

        foreach ($pager as $content) {
            $contentInfo = $this->contentService->loadContentInfo($content->id);

            $data[] = [
                'contentId' => $content->id,
                'name' => $contentInfo->name,
                'language' => $contentInfo->mainLanguageCode,
                'contributor' => $this->userService->loadUser($contentInfo->ownerId),
                'version' => $content->versionInfo->versionNo,
                'type' => $this->contentTypeService->loadContentType($contentInfo->contentTypeId)->getName(),
                'modified' => $content->versionInfo->modificationDate,
            ];
        }

        return $data;
    }
}
