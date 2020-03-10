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
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\SimpleTable;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\SystemInfoTable;

class ContentTypePage extends Page
{
    /** @var string Name by which Page is recognised */
    public const PAGE_NAME = 'Content Type';
    /** @var string Name of actual group */
    public $contentTypeName;

    /** @var string locator for container of Content list */
    public $contentFieldDefinitionsListLocator = 'section:nth-of-type(2)';

    /** @var string locator for container of Content list */
    public $globalPropertiesTableLocator = '.ez-table--list';

    /**
     * @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\AdminList
     */
    public $globalPropertiesTable;
    /**
     * @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\AdminList
     */
    public $fieldsAdminList;

    /**
     * @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\AdminList
     */
    public $contentTypeAdminList;

    public function __construct(UtilityContext $context, string $contentTypeName)
    {
        parent::__construct($context);
        $this->groupName = $contentTypeName;
        $this->siteAccess = 'admin';
        $this->route = '/contenttypegroup/';

        $this->contentTypeAdminList = ElementFactory::createElement(
            $this->context,
            AdminList::ELEMENT_NAME,
            'Content Type',
            SimpleTable::ELEMENT_NAME
        );
        $this->globalPropertiesTable = ElementFactory::createElement(
            $this->context,
            SystemInfoTable::ELEMENT_NAME,
            $this->globalPropertiesTableLocator
        );
        $this->fieldsAdminList = ElementFactory::createElement(
            $this->context,
            AdminList::ELEMENT_NAME,
            'Content',
            DoubleHeaderTable::ELEMENT_NAME,
            $this->contentFieldDefinitionsListLocator
        );
        $this->pageTitle = $contentTypeName;
        $this->pageTitleLocator = '.ez-header h1';
    }

    public function verifyElements(): void
    {
        $this->contentTypeAdminList->verifyVisibility();
        $this->fieldsAdminList->verifyVisibility();
    }
}
