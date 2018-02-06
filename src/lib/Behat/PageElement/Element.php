<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;

/** Abstract for pages elements */
abstract class Element
{
    /** @var int */
    public $defaultTimeout = 5;

    /* \EzSystems\EzPlatformAdminUi\Behat\UtilityContext */
    protected $context;
    protected $fields;

    public function __construct(UtilityContext $context)
    {
        $this->context = $context;
    }

    abstract public function verifyVisibility(): void;
}
