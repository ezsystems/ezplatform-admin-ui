<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\View\Template;

use Pagerfanta\View\Template\TwitterBootstrap4Template;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Template to customize Pagerfanta pagination.
 */
class EzPagerfantaTemplate extends TwitterBootstrap4Template
{
    /**
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator
     *
     * @throws \Symfony\Component\Translation\Exception\InvalidArgumentException
     */
    public function __construct(TranslatorInterface $translator)
    {
        parent::__construct();

        $this->setOptions([
            'prev_message' => '',
            'next_message' => '',
            'active_suffix' => '',
            'css_container_class' => 'pagination ibexa-pagination__navigation',
        ]);
    }
}
