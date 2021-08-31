<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab;

use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * Base class representing Tab. Most use cases should use this abstract
 * as a parent class as it comes with translator and templating services.
 */
abstract class AbstractTab implements TabInterface
{
    /** @var \Twig\Environment */
    protected $twig;

    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    protected $translator;

    /**
     * @param \Twig\Environment $twig
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator
     */
    public function __construct(Environment $twig, TranslatorInterface $translator)
    {
        $this->twig = $twig;
        $this->translator = $translator;
    }
}
