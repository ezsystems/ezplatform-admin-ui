<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab;

use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * Base class representing Tab. Most use cases should use this abstract
 * as a parent class as it comes with translator and templating services.
 */
abstract class AbstractTab implements TabInterface
{
    /** @var Environment */
    protected $twig;

    /** @var TranslatorInterface */
    protected $translator;

    /**
     * @param Environment $twig
     * @param TranslatorInterface $translator
     */
    public function __construct(Environment $twig, TranslatorInterface $translator)
    {
        $this->twig = $twig;
        $this->translator = $translator;
    }
}
