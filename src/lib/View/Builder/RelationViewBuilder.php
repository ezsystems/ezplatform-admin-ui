<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\View\Builder;

use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use eZ\Publish\Core\MVC\Symfony\View\Builder\ViewBuilder;
use eZ\Publish\Core\MVC\Symfony\View\Configurator;
use eZ\Publish\Core\MVC\Symfony\View\ParametersInjector;
use EzSystems\EzPlatformAdminUi\View\RelationView;

/**
 * @internal
 */
class RelationViewBuilder implements ViewBuilder
{
    /** @var \eZ\Publish\API\Repository\Repository */
    private $repository;

    /** @var \eZ\Publish\Core\MVC\Symfony\View\Configurator */
    private $viewConfigurator;

    /** @var \eZ\Publish\Core\MVC\Symfony\View\ParametersInjector */
    private $viewParametersInjector;

    /**
     * @param \eZ\Publish\API\Repository\Repository $repository
     * @param \eZ\Publish\Core\MVC\Symfony\View\Configurator $viewConfigurator
     * @param \eZ\Publish\Core\MVC\Symfony\View\ParametersInjector $viewParametersInjector
     */
    public function __construct(
        Repository $repository,
        Configurator $viewConfigurator,
        ParametersInjector $viewParametersInjector
    ) {
        $this->repository = $repository;
        $this->viewConfigurator = $viewConfigurator;
        $this->viewParametersInjector = $viewParametersInjector;
    }

    /**
     * {@inheritdoc}
     */
    public function matches($argument)
    {
        return 'EzSystems\EzPlatformAdminUiBundle\Controller\ContentViewController::relationViewAction' === $argument;
    }

    /**
     * @param array $parameters
     *
     * @return \EzSystems\EzPlatformAdminUi\View\RelationView
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentType
     */
    public function buildView(array $parameters)
    {
        if (!isset($parameters['contentId'])) {
            throw new InvalidArgumentException(
                'Content',
                'No content could be loaded from parameters'
            );
        }

        $view = new RelationView();

        $view->setContentId((int) $parameters['contentId']);

        try {
            $content = $this->resolveContent($parameters);
            $location = $this->loadLocation($content->contentInfo->mainLocationId);
            $contentType = $this->repository->getContentTypeService()->loadContentType($content->contentInfo->contentTypeId);

            $view->setContent($content);
            $view->setLocation($location);
            $view->setContentType($contentType);
        } catch (UnauthorizedException $exception) {
            $view->setApiException(UnauthorizedException::class);
        }

        $this->viewParametersInjector->injectViewParameters($view, $parameters);
        $this->viewConfigurator->configure($view);

        return $view;
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    private function loadLocation(int $locationId): Location
    {
        return $this->repository->getLocationService()->loadLocation($locationId);
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    private function resolveContent(array $parameters): Content
    {
        if (isset($parameters['content'])) {
            return $parameters['content'];
        }

        return $this->repository->getContentService()->loadContent((int) $parameters['contentId']);
    }
}
