<?php

declare(strict_types=1);

namespace EzPlatformAdminUi\Tab;


use Symfony\Bridge\Twig\Extension\HttpKernelRuntime;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * Base class for Tabs based on a controller action.
 */
abstract class AbstractControllerBasedTab extends AbstractTab
{
    /** @var HttpKernelRuntime */
    private $httpKernelRuntime;

    /**
     * @param Environment $twig
     * @param TranslatorInterface $translator
     * @param UrlGeneratorInterface $urlGenerator
     * @param HttpKernelRuntime $httpKernelRuntime
     */
    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        UrlGeneratorInterface $urlGenerator,
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
     * @return ControllerReference
     */
    abstract function getControllerReference(array $parameters): ControllerReference;
}
