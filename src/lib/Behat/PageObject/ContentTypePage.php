<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageObject;

use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\AdminList;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\DoubleHeaderTable;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\ElementFactory;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\VerticalOrientedTable;

class ContentTypePage extends Page
{
    /** @var string Name by which Page is recognised */
    public const PAGE_NAME = 'Content Type';
    /** @var string Name of actual group */
    public $contentTypeName;

    /** @var string locator for container of Content list */
    public $secondListContainerLocator = 'section:nth-of-type(2)';

    /**
     * @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\AdminList
     */
    public $globalPropertiesAdminList;
    /**
     * @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\AdminList
     */
    public $contentAdminList;

    public function __construct(UtilityContext $context, string $contentTypeName)
    {
        parent::__construct($context);
        $this->groupName = $contentTypeName;
        $this->route = '/admin/contenttypegroup/';
        $this->globalPropertiesAdminList = ElementFactory::createElement(
            $this->context,
            AdminList::ELEMENT_NAME,
            'Global properties',
            VerticalOrientedTable::ELEMENT_NAME
        );
        $this->contentAdminList = ElementFactory::createElement(
            $this->context,
            AdminList::ELEMENT_NAME,
            'Content',
            DoubleHeaderTable::ELEMENT_NAME,
            $this->secondListContainerLocator
        );
        $this->pageTitle = $contentTypeName;
        $this->pageTitleLocator = '.ez-header h1';
    }

    public function verifyElements(): void
    {
        $this->globalPropertiesAdminList->verifyVisibility();
        $this->contentAdminList->verifyVisibility();
    }
}
