<?php
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Templating\Twig;


use EzSystems\EzPlatformAdminUi\UI\Config\Aggregator;
use Twig\Environment;
use Twig_Extension;
use Twig_Extension_GlobalsInterface;

/**
 * Exports `admin_ui_config` providing UI Config as a global Twig variable.
 */
class UiConfigExtension extends Twig_Extension implements Twig_Extension_GlobalsInterface
{
    /** @var Environment */
    protected $twig;

    /** @var Aggregator */
    protected $aggregator;

    /**
     * @param Environment $twig
     * @param Aggregator $aggregator
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
        return ['admin_ui_config' => $this->aggregator->getConfig()];
    }
}
