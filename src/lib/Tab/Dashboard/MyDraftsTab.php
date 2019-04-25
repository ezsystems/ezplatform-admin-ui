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
use EzSystems\EzPlatformAdminUi\Tab\AbstractTab;
use EzSystems\EzPlatformAdminUi\Tab\ConditionalTabInterface;
use EzSystems\EzPlatformAdminUi\Tab\OrderedTabInterface;
use EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;

class MyDraftsTab extends AbstractTab implements OrderedTabInterface, ConditionalTabInterface
{
    private const PAGINATION_PARAM_NAME = 'mydrafts-page';

    /** @var \eZ\Publish\API\Repository\ContentService */
    protected $contentService;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    protected $contentTypeService;

    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    protected $permissionResolver;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory */
    protected $datasetFactory;

    /** @var \Symfony\Component\HttpFoundation\RequestStack */
    private $requestStack;

    /** @var int */
    private $defaultPaginationLimit;

    /**
     * @param \Twig\Environment $twig
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     * @param \eZ\Publish\API\Repository\LocationService $locationService
     * @param \eZ\Publish\API\Repository\PermissionResolver $permissionResolver
     * @param \EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory $datasetFactory
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
     * @param int $defaultPaginationLimit
     */
    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        ContentService $contentService,
        ContentTypeService $contentTypeService,
        LocationService $locationService,
        PermissionResolver $permissionResolver,
        DatasetFactory $datasetFactory,
        RequestStack $requestStack,
        int $defaultPaginationLimit
    ) {
        parent::__construct($twig, $translator);

        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;
        $this->locationService = $locationService;
        $this->permissionResolver = $permissionResolver;
        $this->datasetFactory = $datasetFactory;
        $this->requestStack = $requestStack;
        $this->defaultPaginationLimit = $defaultPaginationLimit;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier(): string
    {
        return 'my-drafts';
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return /** @Desc("Drafts") */
            $this->translator->trans('tab.name.my_drafts', [], 'dashboard');
    }

    /**
     * {@inheritdoc}
     */
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
     */
    public function renderView(array $parameters): string
    {
        $currentPage = $this->requestStack->getCurrentRequest()->query->getInt(
            self::PAGINATION_PARAM_NAME, 1
        );

        $contentDraftsDataset = $this->datasetFactory->contentDrafts();
        $contentDraftsDataset->load();

        $pagination = new Pagerfanta(
            new ArrayAdapter($contentDraftsDataset->getContentDrafts())
        );
        $pagination->setMaxPerPage($this->defaultPaginationLimit);
        $pagination->setCurrentPage(min(max($currentPage, 1), $pagination->getNbPages()));

        return $this->twig->render('@ezdesign/ui/dashboard/tab/my_drafts.html.twig', [
            'data' => $pagination->getCurrentPageResults(),
            'pager' => $pagination,
            'pager_options' => [
                'pageParameter' => '[' . self::PAGINATION_PARAM_NAME . ']',
            ],
        ]);
    }
}
