<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab;

use Symfony\Bridge\Twig\Extension\HttpKernelRuntime;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * Base class for Tabs based on a route.
 */
abstract class AbstractRouteBasedTab extends AbstractTab
{
    /** @var UrlGeneratorInterface */
    protected $urlGenerator;

    /** @var HttpKernelRuntime */
    private $httpKernelRuntime;

    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        int $order,
        UrlGeneratorInterface $urlGenerator,
        HttpKernelRuntime $httpKernelRuntime
    ) {
        parent::__construct($twig, $translator, $order);

        $this->urlGenerator = $urlGenerator;
        $this->httpKernelRuntime = $httpKernelRuntime;
    }

    public function renderView(array $parameters): string
    {
        $route = $this->urlGenerator->generate(
            $this->getRouteName($parameters),
            $this->getRouteParameters($parameters)
        );

        return $this->httpKernelRuntime->renderFragment($route);
    }

    /**
     * Returns route name used to generate path to the resource.
     */
    abstract public function getRouteName(array $parameters): string;

    /**
     * Returns parameters array required to generate path using the router.
     */
    abstract public function getRouteParameters(array $parameters): array;
}
