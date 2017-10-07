<?php
declare(strict_types=1);

namespace EzPlatformAdminUi\Tab\SystemInfo;


use Symfony\Bridge\Twig\Extension\HttpKernelRuntime;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;

class TabFactory
{
    /** @var HttpKernelRuntime */
    protected $httpKernelRuntime;

    /** @var Environment */
    protected $twig;

    /** @var TranslatorInterface */
    protected $translator;

    /**
     * @param Environment $twig
     * @param TranslatorInterface $translator
     * @param HttpKernelRuntime $httpKernelRuntime
     */
    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        HttpKernelRuntime $httpKernelRuntime
    )
    {
        $this->twig = $twig;
        $this->translator = $translator;
        $this->httpKernelRuntime = $httpKernelRuntime;
    }

    /**
     * @param string $collectorIdentifier
     * @param string|null $tabIdentifier
     *
     * @return SystemInfoTab
     */
    public function createTab(string $collectorIdentifier, ?string $tabIdentifier = null): SystemInfoTab
    {
        $tabIdentifier = $tabIdentifier ?? $collectorIdentifier;

        return new SystemInfoTab(
            $this->twig,
            $this->translator,
            $this->httpKernelRuntime,
            $tabIdentifier,
            $collectorIdentifier
        );
    }
}
