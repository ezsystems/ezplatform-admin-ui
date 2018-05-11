<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Component;

use Twig\Environment;

class LinkComponent implements Renderable
{
    /** @var Environment */
    protected $twig;

    /** @var string */
    protected $href;

    /** @var string */
    protected $type;

    /** @var string */
    protected $rel;

    /** @var null|string */
    protected $crossorigin;

    /** @var null|string */
    protected $integrity;

    /**
     * @param Environment $twig
     * @param string $href
     * @param string $type
     * @param string $rel
     * @param string|null $crossorigin
     * @param string|null $integrity
     */
    public function __construct(
        Environment $twig,
        string $href,
        string $type = 'text/css',
        string $rel = 'stylesheet',
        string $crossorigin = null,
        string $integrity = null
    ) {
        $this->twig = $twig;
        $this->href = $href;
        $this->type = $type;
        $this->rel = $rel;
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
        return $this->twig->render('@ezdesign/component/link.html.twig', [
            'href' => $this->href,
            'type' => $this->type,
            'rel' => $this->rel,
            'crossorigin' => $this->crossorigin,
            'integrity' => $this->integrity,
        ] + $parameters);
    }
}
