<?php
declare(strict_types=1);

namespace EzPlatformAdminUi\Tab\Dashboard;


use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use EzPlatformAdminUi\Tab\AbstractTab;
use EzPlatformAdminUi\Tab\OrderedTabInterface;
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

    /**
     * @param Environment $twig
     * @param TranslatorInterface $translator
     * @param ContentService $contentService
     * @param ContentTypeService $contentTypeService
     */
    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        ContentService $contentService,
        ContentTypeService $contentTypeService
    )
    {
        parent::__construct($twig, $translator);

        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;
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

        return $this->twig->render('EzPlatformAdminUiBundle:dashboard/tab:my_drafts.html.twig', [
            'data' => $data,
        ]);
    }
}
