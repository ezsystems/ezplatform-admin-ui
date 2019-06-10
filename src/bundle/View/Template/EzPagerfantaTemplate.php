<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\View\Template;

use Pagerfanta\View\Template\TwitterBootstrap4Template;
use Symfony\Component\Translation\Exception\InvalidArgumentException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Template to customize Pagerfanta pagination.
 */
class EzPagerfantaTemplate extends TwitterBootstrap4Template
{
    /**
     * @param TranslatorInterface $translator
     *
     * @throws InvalidArgumentException
     */
    public function __construct(TranslatorInterface $translator)
    {
        parent::__construct();

        $prevMessage = $translator->trans(
            /** @Desc("Back") */
            'pagination.prev_message',
            [],
            'pagination'
        );

        $nextMessage = $translator->trans(
            /** @Desc("Next") */
            'pagination.next_message',
            [],
            'pagination'
        );

        $this->setOptions(['prev_message' => $prevMessage, 'next_message' => $nextMessage, 'css_container_class' => 'pagination ez-pagination']);
    }
}
