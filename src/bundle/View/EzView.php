<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\View;

use EzSystems\EzPlatformAdminUiBundle\View\Template\EzTemplate;
use Pagerfanta\View\DefaultView;
use Symfony\Component\Translation\TranslatorInterface;

class EzView extends DefaultView
{
    /** @var TranslatorInterface */
    private $translator;

    protected function createDefaultTemplate()
    {
        return new EzTemplate($this->translator);
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
