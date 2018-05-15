<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\BusinessContext;

use Behat\Gherkin\Node\TableNode;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\PageObjectFactory;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\AdminUpdateItemPage;

/** Context for common actions for creating and updating */
class AdminUpdateContext extends BusinessContext
{
    /**
     * @When I set fields
     */
    public function iSetFields(TableNode $table): void
    {
        $updateItemPage = PageObjectFactory::createPage($this->utilityContext, AdminUpdateItemPage::PAGE_NAME);
        $hash = $table->getHash();
        foreach ($hash as $row) {
            $updateItemPage->adminUpdateForm->fillFieldWithValue($row['label'], $row['value']);
        }
    }

    /**
     * @Then fields are set
     */
    public function verifyFieldsAreSet(TableNode $table): void
    {
        $updateItemPage = PageObjectFactory::createPage($this->utilityContext, AdminUpdateItemPage::PAGE_NAME);
        $hash = $table->getHash();
        foreach ($hash as $row) {
            $updateItemPage->adminUpdateForm->verifyFieldHasValue($row['label'], $row['value']);
        }
    }

    /**
     * @When I add field :fieldName to Content Type definition
     */
    public function iAddField(string $fieldName): void
    {
        $updateItemPage = PageObjectFactory::createPage($this->utilityContext, AdminUpdateItemPage::PAGE_NAME);
        $updateItemPage->adminUpdateForm->selectFieldDefinition($fieldName);
        $updateItemPage->adminUpdateForm->clickAddFieldDefinition();
        $updateItemPage->adminUpdateForm->verifyNewFieldDefinitionFormExists($fieldName);
    }

    /**
     * @When I set :field in :containerName to :value
     */
    public function iSetFieldInContainer(string $field, string $containerName, string $value): void
    {
        $updateItemPage = PageObjectFactory::createPage($this->utilityContext, AdminUpdateItemPage::PAGE_NAME);
        $updateItemPage->adminUpdateForm->expandFieldDefinition($containerName);
        $updateItemPage->adminUpdateForm->fillFieldWithValue($field, $value, $containerName);
    }

    /**
     * @When I select options from :selectName
     */
    public function iSelectOptionsFrom(string $selectName, TableNode $options): void
    {
        $optionsHash = $options->getHash();
        $this->utilityContext->selectOption($selectName, $optionsHash[0]['option']);
        for ($i = 1; $i < count($optionsHash); ++$i) {
            $this->utilityContext->additionallySelectOption($selectName, $optionsHash[$i]['option']);
        }
    }
}
