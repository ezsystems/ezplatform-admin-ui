<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
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
    /** @var HttpKernelRuntime */
    protected $httpKernelRuntime;

    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        int $order,
        HttpKernelRuntime $httpKernelRuntime
    ) {
        parent::__construct($twig, $translator, $order);

        $this->httpKernelRuntime = $httpKernelRuntime;
    }

    public function renderView(array $parameters): string
    {
        return $this->httpKernelRuntime->renderFragment($this->getControllerReference($parameters));
    }

    abstract public function getControllerReference(array $parameters): ControllerReference;
}
