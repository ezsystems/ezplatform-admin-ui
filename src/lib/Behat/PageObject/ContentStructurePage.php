<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageObject;

use EzSystems\EzPlatformAdminUi\Behat\PageElement\RightMenu;
use EzSystems\EzPlatformAdminUi\Behat\UtilityContext;

class ContentStructurePage extends Page
{
    /** @var string Route under which the Page is available */
    protected $route = '/admin/content/location';

    /** @var string Name by which Page is recognised */
    public const PAGE_NAME = 'ContentStructure';

    protected $rightMenu;

    public function __construct(UtilityContext $context)
    {
        parent::__construct($context);
        $this->rightMenu = new RightMenu($this->context);
    }

    public function create()
    {
        $this->rightMenu->clickButton('Create');
    }

    public function performAction($actionName)
    {
        $this->rightMenu->clickButton($actionName);
    }
}