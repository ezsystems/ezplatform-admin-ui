<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab;

use Symfony\Bridge\Twig\Extension\HttpKernelRuntime;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * Base class for Tabs based on a controller action.
 */
abstract class AbstractControllerBasedTab extends AbstractTab
{
    /** @var \Symfony\Bridge\Twig\Extension\HttpKernelRuntime */
    protected $httpKernelRuntime;

    /**
     * @param \Twig\Environment $twig
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator
     * @param \Symfony\Bridge\Twig\Extension\HttpKernelRuntime $httpKernelRuntime
     */
    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        HttpKernelRuntime $httpKernelRuntime
    ) {
        parent::__construct($twig, $translator);

        $this->httpKernelRuntime = $httpKernelRuntime;
    }

    public function renderView(array $parameters): string
    {
        return $this->httpKernelRuntime->renderFragment($this->getControllerReference($parameters));
    }

    /**
     * Returns ControllerReference used to render the tab.
     *
     * @param array $parameters
     *
     * @return \Symfony\Component\HttpKernel\Controller\ControllerReference
     */
    abstract public function getControllerReference(array $parameters): ControllerReference;
}
