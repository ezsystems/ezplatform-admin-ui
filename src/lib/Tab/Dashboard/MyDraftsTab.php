<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab\Dashboard;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use EzSystems\EzPlatformAdminUi\Pagination\Pagerfanta\ContentDraftAdapter;
use EzSystems\EzPlatformAdminUi\Tab\AbstractTab;
use EzSystems\EzPlatformAdminUi\Tab\ConditionalTabInterface;
use EzSystems\EzPlatformAdminUi\Tab\OrderedTabInterface;
use EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class MyDraftsTab extends AbstractTab implements OrderedTabInterface, ConditionalTabInterface
{
    private const PAGINATION_PARAM_NAME = 'mydrafts-page';

    /** @var \eZ\Publish\API\Repository\ContentService */
    protected $contentService;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    protected $contentTypeService;

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    protected $permissionResolver;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory */
    protected $datasetFactory;

    /** @var \Symfony\Component\HttpFoundation\RequestStack */
    private $requestStack;

    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        ContentService $contentService,
        ContentTypeService $contentTypeService,
        PermissionResolver $permissionResolver,
        DatasetFactory $datasetFactory,
        RequestStack $requestStack,
        ConfigResolverInterface $configResolver
    ) {
        parent::__construct($twig, $translator);

        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;
        $this->permissionResolver = $permissionResolver;
        $this->datasetFactory = $datasetFactory;
        $this->requestStack = $requestStack;
        $this->configResolver = $configResolver;
    }

    /**
     * @inheritdoc
     */
    public function getIdentifier(): string
    {
        return 'my-drafts';
    }

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return /** @Desc("Drafts") */
            $this->translator->trans('tab.name.my_drafts', [], 'dashboard');
    }

    /**
     * @inheritdoc
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
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function renderView(array $parameters): string
    {
        $currentPage = $this->requestStack->getCurrentRequest()->query->getInt(
            self::PAGINATION_PARAM_NAME, 1
        );

        $pagination = new Pagerfanta(
            new ContentDraftAdapter($this->contentService, $this->datasetFactory)
        );
        $pagination->setMaxPerPage($this->configResolver->getParameter('pagination.content_draft_limit'));
        $pagination->setCurrentPage(min(max($currentPage, 1), $pagination->getNbPages()));

        return $this->twig->render('@ezdesign/ui/dashboard/tab/my_draft_list.html.twig', [
            'data' => $pagination->getCurrentPageResults(),
            'pager' => $pagination,
            'pager_options' => [
                'pageParameter' => '[' . self::PAGINATION_PARAM_NAME . ']',
            ],
        ]);
    }
}
