<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\BusinessContext;

use Behat\Gherkin\Node\TableNode;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\ContentUpdateItemPage;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\PageObjectFactory;

class ContentUpdateContext extends BusinessContext
{
    /**
     * @When I set content fields
     */
    public function iSetFields(TableNode $table): void
    {
        $updateItemPage = PageObjectFactory::createPage($this->utilityContext, ContentUpdateItemPage::PAGE_NAME, '');
        $hash = $table->getHash();
        foreach ($hash as $row) {
            $values = $this->filterOutNonEmptyValues($row);
            $updateItemPage->contentUpdateForm->fillFieldWithValue($row['label'], $values);
        }
    }

    private function filterOutNonEmptyValues(array $parameters): array
    {
        $values = $parameters;
        unset($values['label']);

        return array_filter($values, function ($element) { return !empty($element) || $element === 0;});
    }

    /**
     * @Then content fields are set
     */
    public function verifyFieldsAreSet(TableNode $table): void
    {
        $updateItemPage = PageObjectFactory::createPage($this->utilityContext, ContentUpdateItemPage::PAGE_NAME, '');
        $hash = $table->getHash();
        foreach ($hash as $row) {
            $updateItemPage->contentUpdateForm->verifyFieldHasValue($row);
        }
    }

    /**
     * @When I click on the close button
     */
    public function iClickCloseButton(): void
    {
        $updateItemPage = PageObjectFactory::createPage($this->utilityContext, ContentUpdateItemPage::PAGE_NAME, '');
        $updateItemPage->contentUpdateForm->closeUpdateForm();
    }
}
