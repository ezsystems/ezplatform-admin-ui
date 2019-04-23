<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use eZ\Publish\API\Repository\ContentService;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Draft\ContentRemoveData;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Form\SubmitHandler;
use EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory;
use EzSystems\EzPlatformAdminUi\UI\Value\Content\VersionId;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ContentDraftController extends Controller
{
    private const PAGINATION_PARAM_NAME = 'page';

    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory */
    private $datasetFactory;

    /** @var \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory */
    private $formFactory;

    /** @var \EzSystems\EzPlatformAdminUi\Form\SubmitHandler */
    private $submitHandler;

    /** @var int */
    private $defaultPaginationLimit;

    /**
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory $datasetFactory
     * @param \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory $formFactory
     * @param \EzSystems\EzPlatformAdminUi\Form\SubmitHandler $submitHandler
     * @param int $defaultPaginationLimit
     */
    public function __construct(
        ContentService $contentService,
        DatasetFactory $datasetFactory,
        FormFactory $formFactory,
        SubmitHandler $submitHandler,
        int $defaultPaginationLimit
    ) {
        $this->contentService = $contentService;
        $this->datasetFactory = $datasetFactory;
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
        $this->defaultPaginationLimit = $defaultPaginationLimit;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request): Response
    {
        $currentPage = $request->query->getInt(self::PAGINATION_PARAM_NAME, 1);

        $contentDraftsDataset = $this->datasetFactory->contentDrafts();
        $contentDraftsDataset->load();

        $pagination = new Pagerfanta(
            new ArrayAdapter($contentDraftsDataset->getContentDrafts())
        );
        $pagination->setMaxPerPage($this->defaultPaginationLimit);
        $pagination->setCurrentPage(min($currentPage, $pagination->getNbPages()));

        $removeContentDraftForm = $this->formFactory->removeContentDraft(
            $this->createContentRemoveData($pagination)
        );

        return $this->render('@ezdesign/content/draft/list.html.twig', [
            'pager' => $pagination,
            'form_remove' => $removeContentDraftForm->createView(),
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeAction(Request $request): Response
    {
        $form = $this->formFactory->removeContentDraft();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (ContentRemoveData $data) {
                foreach (array_keys($data->getVersions()) as $version) {
                    $versionId = VersionId::fromString($version);

                    $this->contentService->deleteVersion(
                        $this->contentService->loadVersionInfoById(
                            $versionId->getContentId(),
                            $versionId->getVersionNo()
                        )
                    );
                }

                return $this->redirectToRoute('ezplatform.content_draft.list');
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirectToRoute('ezplatform.content_draft.list');
    }

    /**
     * @param \Pagerfanta\Pagerfanta $pagerfanta
     *
     * @return \EzSystems\EzPlatformAdminUi\Form\Data\Content\Draft\ContentRemoveData
     */
    private function createContentRemoveData(Pagerfanta $pagerfanta): ContentRemoveData
    {
        $versions = array_column($pagerfanta->getCurrentPageResults(), 'id');

        return new ContentRemoveData(
            array_combine($versions, array_fill_keys($versions, false))
        );
    }
}
