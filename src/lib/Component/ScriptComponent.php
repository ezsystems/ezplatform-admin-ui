<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Component;

use Twig\Environment;

class ScriptComponent implements Renderable
{
    /** @var Environment */
    protected $twig;

    /** @var string */
    protected $src;

    /** @var string */
    protected $type;

    /** @var null|string */
    protected $async;

    /** @var null|string */
    protected $defer;

    /** @var null|string */
    protected $crossorigin;

    /** @var null|string */
    protected $integrity;

    /**
     * @param Environment $twig
     * @param string $src
     * @param string $type
     * @param string|null $async
     * @param string|null $defer
     * @param string|null $crossorigin
     * @param string|null $integrity
     */
    public function __construct(
        Environment $twig,
        string $src,
        string $type = 'text/javascript',
        string $async = null,
        string $defer = null,
        string $crossorigin = null,
        string $integrity = null
    ) {
        $this->twig = $twig;
        $this->src = $src;
        $this->type = $type;
        $this->async = $async;
        $this->defer = $defer;
        $this->crossorigin = $crossorigin;
        $this->integrity = $integrity;
    }

    /**
     * @param array $parameters
     *
     * @return string
     */
    public function render(array $parameters = []): string
    {
        return $this->twig->render('@ezdesign/ui/component/script.html.twig', [
            'src' => $this->src,
            'type' => $this->type,
            'async' => $this->async,
            'defer' => $this->defer,
            'crossorigin' => $this->crossorigin,
            'integrity' => $this->integrity,
        ] + $parameters);
    }
}
