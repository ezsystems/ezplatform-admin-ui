<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\View\Provider\RelationView;

use eZ\Publish\Core\MVC\Symfony\Matcher\MatcherFactoryInterface;
use eZ\Publish\Core\MVC\Symfony\View\View;
use eZ\Publish\Core\MVC\Symfony\View\ViewProvider;
use EzSystems\EzPlatformAdminUi\View\RelationView;
use Symfony\Component\HttpKernel\Controller\ControllerReference;

/**
 * View provider based on configuration.
 */
class Configured implements ViewProvider
{
    /**
     * @var \eZ\Publish\Core\MVC\Symfony\Matcher\MatcherFactoryInterface
     */
    protected $matcherFactory;

    /**
     * @param \eZ\Publish\Core\MVC\Symfony\Matcher\MatcherFactoryInterface $matcherFactory
     */
    public function __construct(MatcherFactoryInterface $matcherFactory)
    {
        $this->matcherFactory = $matcherFactory;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentType
     */
    public function getView(View $view)
    {
        if (($configHash = $this->matcherFactory->match($view)) === null) {
            return null;
        }

        return $this->buildRelationView($configHash);
    }

    /**
     * Builds a RelationView object from $viewConfig.
     *
     * @param array $viewConfig
     *
     * @return \EzSystems\EzPlatformAdminUi\View\RelationView
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentType
     */
    protected function buildRelationView(array $viewConfig): RelationView
    {
        $view = new RelationView();
        $view->setConfigHash($viewConfig);
        if (isset($viewConfig['template'])) {
            $view->setTemplateIdentifier($viewConfig['template']);
        }
        if (isset($viewConfig['controller'])) {
            $view->setControllerReference(new ControllerReference($viewConfig['controller']));
        }
        if (isset($viewConfig['params']) && is_array($viewConfig['params'])) {
            $view->addParameters($viewConfig['params']);
        }

        return $view;
    }
}
