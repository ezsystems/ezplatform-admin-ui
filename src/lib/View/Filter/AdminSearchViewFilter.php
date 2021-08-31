<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\View\Filter;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\SectionService;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\Core\MVC\Symfony\View\Event\FilterViewBuilderParametersEvent;
use eZ\Publish\Core\MVC\Symfony\View\ViewEvents;
use EzSystems\EzPlatformAdminUi\Form\Type\Search\SearchType;
use EzSystems\EzPlatformAdminUi\Specification\SiteAccess\IsAdmin;
use Ibexa\Platform\Bundle\Search\Form\Data\SearchData;
use Ibexa\Platform\Search\View\SearchViewFilter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AdminSearchViewFilter implements EventSubscriberInterface
{
    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    /** @var \Symfony\Component\Form\FormFactoryInterface */
    private $formFactory;

    /** @var \eZ\Publish\API\Repository\SectionService */
    private $sectionService;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var array */
    private $siteAccessGroups;

    /** @var \Ibexa\Platform\Search\View\SearchViewFilter */
    private $innerFilter;

    /** @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface */
    private $urlGenerator;

    public function __construct(
        ConfigResolverInterface $configResolver,
        FormFactoryInterface $formFactory,
        SectionService $sectionService,
        ContentTypeService $contentTypeService,
        array $siteAccessGroups,
        SearchViewFilter $innerFilter,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->configResolver = $configResolver;
        $this->formFactory = $formFactory;
        $this->sectionService = $sectionService;
        $this->contentTypeService = $contentTypeService;
        $this->siteAccessGroups = $siteAccessGroups;
        $this->innerFilter = $innerFilter;
        $this->urlGenerator = $urlGenerator;
    }

    public static function getSubscribedEvents()
    {
        return [ViewEvents::FILTER_BUILDER_PARAMETERS => 'handleSearchForm'];
    }

    /**
     * @throws \Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException
     * @throws \Symfony\Component\OptionsResolver\Exception\OptionDefinitionException
     * @throws \Symfony\Component\OptionsResolver\Exception\NoSuchOptionException
     * @throws \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function handleSearchForm(FilterViewBuilderParametersEvent $event): void
    {
        $controllerAction = $event->getParameters()->get('_controller');

        if (
            'Ibexa\Platform\Bundle\Search\Controller\SearchController::searchAction' !== $controllerAction
        ) {
            return;
        }

        $request = $event->getRequest();

        if (!$this->isAdminSiteAccess($request)) {
            $this->innerFilter->handleSearchForm($event);

            return;
        }

        $search = $request->query->get('search');
        $limit = isset($search['limit']) ? (int)$search['limit'] : $this->configResolver->getParameter('pagination.search_limit');
        $page = isset($search['page']) ? (int)$search['page'] : 1;
        $query = $search['query'] ?? '';
        $section = null;
        $creator = null;
        $contentTypes = [];
        $lastModified = $search['last_modified'] ?? [];
        $created = $search['created'] ?? [];
        $subtree = $search['subtree'] ?? null;
        $searchLanguage = null;

        if (!empty($search['section'])) {
            try {
                $section = $this->sectionService->loadSection((int)$search['section']);
            } catch (NotFoundException $e) {
                $section = null;
            }
        }

        if (!empty($search['content_types']) && \is_array($search['content_types'])) {
            foreach ($search['content_types'] as $identifier) {
                $contentTypes[] = $this->contentTypeService->loadContentTypeByIdentifier($identifier);
            }
        }

        $form = $this->formFactory->create(
            SearchType::class,
            new SearchData(
                $limit,
                $page,
                $query,
                $section,
                $contentTypes,
                $lastModified,
                $created,
                $creator,
                $subtree,
                $searchLanguage
            ),
            [
                'method' => Request::METHOD_GET,
                'csrf_protection' => false,
                'action' => $this->urlGenerator->generate('ezplatform.search'),
            ]
        );

        $event->getParameters()->add([
            'form' => $form->handleRequest($request),
        ]);
    }

    protected function isAdminSiteAccess(Request $request): bool
    {
        return (new IsAdmin($this->siteAccessGroups))->isSatisfiedBy($request->attributes->get('siteaccess'));
    }
}
