<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab\URLManagement;

use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\URLService;
use eZ\Publish\API\Repository\Values\URL\Query\Criterion;
use eZ\Publish\API\Repository\Values\URL\Query\SortClause;
use eZ\Publish\API\Repository\Values\URL\URLQuery;
use EzSystems\EzPlatformAdminUi\Form\Data\URL\URLListData;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Form\SubmitHandler;
use EzSystems\EzPlatformAdminUi\Notification\TranslatableNotificationHandlerInterface;
use EzSystems\EzPlatformAdminUi\Pagination\Pagerfanta\URLSearchAdapter;
use EzSystems\EzPlatformAdminUi\Tab\AbstractTab;
use EzSystems\EzPlatformAdminUi\Tab\ConditionalTabInterface;
use EzSystems\EzPlatformAdminUi\Tab\OrderedTabInterface;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class LinkManagerTab extends AbstractTab implements OrderedTabInterface, ConditionalTabInterface
{
    public const URI_FRAGMENT = 'ibexa-tab-link-manager-link-manager';
    private const DEFAULT_MAX_PER_PAGE = 10;

    /** @var \eZ\Publish\API\Repository\URLService */
    private $urlService;

    /** @var \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory */
    private $formFactory;

    /** @var \EzSystems\EzPlatformAdminUi\Form\SubmitHandler */
    private $submitHandler;

    /** @var \EzSystems\EzPlatformAdminUi\Notification\TranslatableNotificationHandlerInterface */
    private $notificationHandler;

    /** @var \Symfony\Component\HttpFoundation\RequestStack */
    private $requestStack;

    /**
     * @var \eZ\Publish\API\Repository\PermissionResolver
     */
    private $permissionResolver;

    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        URLService $urlService,
        FormFactory $formFactory,
        SubmitHandler $submitHandler,
        TranslatableNotificationHandlerInterface $notificationHandler,
        RequestStack $request,
        PermissionResolver $permissionResolver
    ) {
        parent::__construct($twig, $translator);

        $this->urlService = $urlService;
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
        $this->notificationHandler = $notificationHandler;
        $this->requestStack = $request;
        $this->permissionResolver = $permissionResolver;
    }

    public function getIdentifier(): string
    {
        return 'link-manager';
    }

    public function getName(): string
    {
        return /** @Desc("Link manager") */
            $this->translator->trans('tab.name.link_manager', [], 'linkmanager');
    }

    public function getOrder(): int
    {
        return 10;
    }

    /**
     * @param array $parameters
     *
     * @return bool
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function evaluate(array $parameters): bool
    {
        return false !== $this->permissionResolver->hasAccess('url', 'view');
    }

    public function renderView(array $parameters): string
    {
        $data = new URLListData();

        $form = $this->formFactory->createUrlListForm($data, '', [
            'method' => Request::METHOD_GET,
            'csrf_protection' => false,
        ]);

        $form->handleRequest($this->requestStack->getCurrentRequest());

        if ($form->isSubmitted() && !$form->isValid()) {
            throw new BadRequestHttpException();
        }

        $urls = new Pagerfanta(new URLSearchAdapter(
            $this->buildListQuery($data),
            $this->urlService
        ));

        $urls->setCurrentPage($data->page);
        $urls->setMaxPerPage($data->limit ? $data->limit : self::DEFAULT_MAX_PER_PAGE);

        return $this->twig->render('@ezdesign/link_manager/list.html.twig', [
            'form' => $form->createView(),
            'can_edit' => $this->permissionResolver->hasAccess('url', 'update'),
            'urls' => $urls,
        ]);
    }

    /**
     * Builds URL criteria from list data.
     *
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\URL\URLListData $data
     *
     * @return \eZ\Publish\API\Repository\Values\URL\URLQuery
     */
    private function buildListQuery(URLListData $data): URLQuery
    {
        $query = new URLQuery();
        $query->sortClauses = [
            new SortClause\URL(),
        ];

        $criteria = [
            new Criterion\VisibleOnly(),
        ];

        if ($data->searchQuery !== null) {
            $criteria[] = new Criterion\Pattern($data->searchQuery);
        }

        if ($data->status !== null) {
            $criteria[] = new Criterion\Validity($data->status);
        }

        $query->filter = new Criterion\LogicalAnd($criteria);

        return $query;
    }
}
