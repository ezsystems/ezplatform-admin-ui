<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Component;

use Twig\Environment;

class TwigComponent implements Renderable
{
    /** @var string */
    protected $template;

    /** @var \Twig\Environment */
    protected $twig;

    /** @var array */
    protected $parameters;

    /**
     * @param \Twig\Environment $twig
     * @param string $template
     * @param array $parameters
     */
    public function __construct(
        Environment $twig,
        string $template,
        array $parameters = []
    ) {
        $this->twig = $twig;
        $this->template = $template;
        $this->parameters = $parameters;
    }

    /**
     * @param array $parameters
     *
     * @return string
     */
    public function render(array $parameters = []): string
    {
        return $this->twig->render($this->template, $parameters + $this->parameters);
    }
}
