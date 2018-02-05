<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

use EzSystems\EzPlatformAdminUi\Behat\UtilityContext;

abstract class Element
{
    /** @var int */
    public $defaultTimeout = 5;

    /* \EzSystems\EzPlatformAdminUi\Behat\UtilityContext */
    protected $context;

    public function __construct(UtilityContext $context)
    {
        $this->context = $context;
    }

    public function verifyVisibility(): void
    {

    }
}
