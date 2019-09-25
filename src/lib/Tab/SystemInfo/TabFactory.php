<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab\SystemInfo;

use Symfony\Bridge\Twig\Extension\HttpKernelRuntime;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class TabFactory
{
    /** @var HttpKernelRuntime */
    protected $httpKernelRuntime;

    /** @var Environment */
    protected $twig;

    /** @var TranslatorInterface */
    protected $translator;

    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        HttpKernelRuntime $httpKernelRuntime
    ) {
        $this->twig = $twig;
        $this->translator = $translator;
        $this->httpKernelRuntime = $httpKernelRuntime;
    }

    public function createTab(string $collectorIdentifier, ?string $tabIdentifier = null, int $order = 0): SystemInfoTab
    {
        $tabIdentifier = $tabIdentifier ?? $collectorIdentifier;

        return new SystemInfoTab(
            $this->twig,
            $this->translator,
            $order,
            $this->httpKernelRuntime,
            $tabIdentifier,
            $collectorIdentifier
        );
    }
}
