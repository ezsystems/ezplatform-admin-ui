<?php
declare(strict_types=1);

namespace EzPlatformAdminUi\Tab\Dashboard;


use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\Core\Pagination\Pagerfanta\ContentSearchAdapter;
use EzPlatformAdminUi\Tab\AbstractTab;
use EzPlatformAdminUi\Tab\OrderedTabInterface;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;

class EveryoneMediaTab extends AbstractTab implements OrderedTabInterface
{
    /** @var PagerContentToDataMapper */
    protected $pagerContentToDataMapper;

    /** @var SearchService */
    protected $searchService;

    /**
     * @param Environment $twig
     * @param TranslatorInterface $translator
     * @param PagerContentToDataMapper $pagerContentToDataMapper
     * @param SearchService $searchService
     */
    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        PagerContentToDataMapper $pagerContentToDataMapper,
        SearchService $searchService
    )
    {
        parent::__construct($twig, $translator);

        $this->pagerContentToDataMapper = $pagerContentToDataMapper;
        $this->searchService = $searchService;
    }

    public function getIdentifier(): string
    {
        return 'everyone-media';
    }

    public function getName(): string
    {
        return /** @Desc("Media") */
            $this->translator->trans('tab.name.everyone_media', [], 'dashboard');
    }

    public function getOrder(): int
    {
        return 200;
    }

    public function renderView(array $parameters): string
    {
        /** @todo Handle pagination */
        $page = 1;
        $limit = 10;

        /** @todo subtree shouldn't be hardcoded! */
        $pager = new Pagerfanta(
            new ContentSearchAdapter(
                new SubtreeQuery('/1/43/'),
                $this->searchService
            )
        );
        $pager->setMaxPerPage($limit);
        $pager->setCurrentPage($page);

        return $this->twig->render('EzPlatformAdminUiBundle:dashboard/tab:all_media.html.twig', [
            'data' => $this->pagerContentToDataMapper->map($pager),
        ]);
    }
}
