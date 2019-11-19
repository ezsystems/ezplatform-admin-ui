<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\View\Builder;

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
    /** @var \eZ\Publish\Core\MVC\Symfony\View\Configurator */
    private $viewConfigurator;

    /** @var \eZ\Publish\Core\MVC\Symfony\View\ParametersInjector */
    private $viewParametersInjector;

    public function __construct(
        Configurator $viewConfigurator,
        ParametersInjector $viewParametersInjector
    ) {
        $this->viewConfigurator = $viewConfigurator;
        $this->viewParametersInjector = $viewParametersInjector;
    }

    public function matches($argument): bool
    {
        return 'EzSystems\EzPlatformAdminUiBundle\Controller\ContentViewController::relationViewAction' === $argument;
    }

    /**
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentType
     */
    public function buildView(array $parameters): RelationView
    {
        if (!isset($parameters['contentId'])) {
            throw new InvalidArgumentException(
                'Content',
                'No content could be loaded from parameters'
            );
        }

        $view = new RelationView();
        $view->setContentId((int) $parameters['contentId']);

        $this->viewParametersInjector->injectViewParameters($view, $parameters);
        $this->viewConfigurator->configure($view);

        return $view;
    }
}
