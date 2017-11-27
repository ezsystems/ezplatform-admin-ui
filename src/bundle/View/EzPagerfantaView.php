<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\View;

use EzSystems\EzPlatformAdminUiBundle\View\Template\EzPagerfantaTemplate;
use Pagerfanta\View\DefaultView;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * View to render Pagerfanta pagination.
 */
class EzPagerfantaView extends DefaultView
{
    /** @var TranslatorInterface */
    private $translator;

    protected function createDefaultTemplate()
    {
        return new EzPagerfantaTemplate($this->translator);
    }

    protected function getDefaultProximity()
    {
        return 3;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ez';
    }
}
