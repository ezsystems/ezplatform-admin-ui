<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables;

use EzSystems\Behat\Browser\Context\BrowserContext;
use EzSystems\Behat\Browser\Element\Element;
use EzSystems\Behat\Browser\Factory\ElementFactory;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Pagination;

abstract class ItemsList extends Element
{
    public function __construct(BrowserContext $context, string $containerLocator)
    {
        parent::__construct($context);
        $this->fields['list'] = $containerLocator;
    }

    public function getItemCount(): int
    {
        return count($this->context->getSession()->getPage()->findAll('css', $this->fields['listElement']));
    }

    /**
     * Check if list contains list element with given name.
     *
     * @param string $name
     *
     * @return bool
     */
    public function isElementOnCurrentPage(string $name): bool
    {
        return $this->context->getElementByText($name, $this->fields['listElement']) !== null;
    }

    public function isElementInTable(string $contentName): bool
    {
        $pagination = ElementFactory::createElement($this->context, Pagination::ELEMENT_NAME);

        while (true) {
            if ($this->isElementOnCurrentPage($contentName)) {
                return true;
            }

            if ($pagination->isNextButtonActive()) {
                $pagination->clickNextButton();
            } else {
                break;
            }
        }

        return false;
    }
}
