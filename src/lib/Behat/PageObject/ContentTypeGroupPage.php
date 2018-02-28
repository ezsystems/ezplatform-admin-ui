<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageObject;

use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\AdminList;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\ElementFactory;

class ContentTypeGroupPage extends Page
{
    /** @var string Name by which Page is recognised */
    public const PAGE_NAME = 'Content Type Group';
    /** @var string Name of actual group */
    public $groupName;

    /**
     * @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\AdminList
     */
    public $adminList;

    public function __construct(UtilityContext $context, string $groupName)
    {
        parent::__construct($context);
        $this->route = '/admin/contenttypegroup/';
        $this->groupName = $groupName;
        $this->adminList = ElementFactory::createElement($this->context, AdminList::ELEMENT_NAME, sprintf('Content Types in %s', $this->groupName));
        $this->pageTitle = $groupName;
        $this->pageTitleLocator = '.ez-header h1';
    }

    /**
     * Verifies that all necessary elements are visible.
     */
    public function verifyElements(): void
    {
        $this->adminList->verifyVisibility();
    }
}
