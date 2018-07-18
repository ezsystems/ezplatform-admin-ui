<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Templating\Twig;

use EzSystems\EzPlatformAdminUi\UI\Config\Aggregator;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use ProxyManager\Proxy\LazyLoadingInterface;
use Twig\Environment;
use Twig_Extension;
use Twig_Extension_GlobalsInterface;
use EzSystems\EzPlatformAdminUi\UI\Config\ConfigWrapper;

/**
 * Exports `admin_ui_config` providing UI Config as a global Twig variable.
 */
class UiConfigExtension extends Twig_Extension implements Twig_Extension_GlobalsInterface
{
    /** @var \Twig\Environment */
    protected $twig;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Config\Aggregator */
    protected $aggregator;

    /**
     * @param \Twig\Environment $twig
     * @param \EzSystems\EzPlatformAdminUi\UI\Config\Aggregator $aggregator
     */
    public function __construct(Environment $twig, Aggregator $aggregator)
    {
        $this->twig = $twig;
        $this->aggregator = $aggregator;
    }

    /**
     * @return array
     */
    public function getGlobals(): array
    {
        return [
            'admin_ui_config' => $this->createConfigWrapper(),
        ];
    }

    /**
     * Create lazy loaded configuration.
     *
     * @return \EzSystems\EzPlatformAdminUi\UI\Config\ConfigWrapper
     */
    private function createConfigWrapper(): ConfigWrapper
    {
        $factory = new LazyLoadingValueHolderFactory();
        $initializer = function (&$wrappedObject, LazyLoadingInterface $proxy, $method, array $parameters, &$initializer) {
            $initializer = null;
            $wrappedObject = new ConfigWrapper($this->aggregator->getConfig());

            return true;
        };

        return $factory->createProxy(ConfigWrapper::class, $initializer);
    }
}
