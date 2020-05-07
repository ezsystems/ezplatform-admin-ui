<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageObject;

use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\ElementFactory;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\DashboardTable;
use PHPUnit\Framework\Assert;

class SearchPage extends Page
{
    public const PAGE_NAME = 'Search';

    public function __construct(UtilityContext $context)
    {
        parent::__construct($context);
        $this->route = '/search';
        $this->pageTitle = 'Search';
        $this->pageTitleLocator = '.ez-page-title__content-name';
        $this->fields = [
            'inputField' => '.ez-search-form #search_query',
            'buttonSearch' => '.ez-btn--search',
        ];
    }

    public function search(string $contentItemName): void
    {
        $this->context->findElement($this->fields['inputField'])->setValue($contentItemName);
        $this->context->findElement($this->fields['buttonSearch'])->click();
    }

    public function verifyItemInSearchResults($contentItemName): void
    {
        $table = ElementFactory::createElement($this->context, DashboardTable::ELEMENT_NAME, '.container');
        Assert::assertTrue($table->isElementInTable($contentItemName));
    }

    public function verifyElements(): void
    {
    }
}
