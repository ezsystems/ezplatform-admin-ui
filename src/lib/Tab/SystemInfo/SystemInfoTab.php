<?php
declare(strict_types=1);

namespace EzPlatformAdminUi\Tab\SystemInfo;


use EzPlatformAdminUi\Tab\AbstractControllerBasedTab;
use Symfony\Bridge\Twig\Extension\HttpKernelRuntime;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;

class SystemInfoTab extends AbstractControllerBasedTab
{
    /** @var string */
    protected $tabIdentifier;

    /** @var string */
    protected $collectorIdentifier;

    /**
     * @param Environment $twig
     * @param TranslatorInterface $translator
     * @param HttpKernelRuntime $httpKernelRuntime
     * @param string $tabIdentifier
     * @param string $collectorIdentifier
     */
    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        HttpKernelRuntime $httpKernelRuntime,
        string $tabIdentifier,
        string $collectorIdentifier
    )
    {
        parent::__construct($twig, $translator, $httpKernelRuntime);

        $this->tabIdentifier = $tabIdentifier;
        $this->collectorIdentifier = $collectorIdentifier;
    }

    function getControllerReference(array $parameters): ControllerReference
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
        return $this->translator->trans(sprintf('tab.name.%s', $this->tabIdentifier), [], 'systeminfo');
    }
}
