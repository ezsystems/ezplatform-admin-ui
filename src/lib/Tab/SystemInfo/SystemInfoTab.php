<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab\SystemInfo;

use EzSystems\EzPlatformAdminUi\Tab\AbstractControllerBasedTab;
use Symfony\Bridge\Twig\Extension\HttpKernelRuntime;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class SystemInfoTab extends AbstractControllerBasedTab
{
    /** @var string */
    protected $tabIdentifier;

    /** @var string */
    protected $collectorIdentifier;

    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        int $order,
        HttpKernelRuntime $httpKernelRuntime,
        string $tabIdentifier,
        string $collectorIdentifier
    ) {
        parent::__construct($twig, $translator, $order, $httpKernelRuntime);

        $this->tabIdentifier = $tabIdentifier;
        $this->collectorIdentifier = $collectorIdentifier;
    }

    public function getControllerReference(array $parameters): ControllerReference
    {
        return new ControllerReference('support_tools.view.controller:viewInfoAction', [
            'systemInfoIdentifier' => $this->collectorIdentifier,
            'viewType' => 'pjax_tab',
        ]);
    }

    public function getIdentifier(): string
    {
        return $this->tabIdentifier;
    }

    public function getName(): string
    {
        return /** @Ignore */$this->translator->trans(sprintf('tab.name.%s', $this->tabIdentifier), [], 'systeminfo');
    }
}
