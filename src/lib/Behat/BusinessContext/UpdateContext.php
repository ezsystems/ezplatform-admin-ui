<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\BusinessContext;

use Behat\Gherkin\Node\TableNode;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\PageObjectFactory;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\UpdateItemPage;

/** Context for common actions for creating and updating */
class UpdateContext extends BusinessContext
{
    /**
     * @When I set :field to :value
     * @When I set :field as empty
     */
    public function iSetField(string $field, string $value = ''): void
    {
        PageObjectFactory::createPage($this->utilityContext, UpdateItemPage::PAGE_NAME)
            ->updateForm->fillFieldWithValue($field, $value);
    }

    /**
     * @When I set fields
     */
    public function iSetFields(TableNode $table): void
    {
        $hash = $table->getHash();
        foreach ($hash as $row) {
            $this->iSetField($row['label'], $row['value']);
        }
    }

    /**
     * @When I add field :fieldName to Content Type definition
     */
    public function iAddField(string $fieldName): void
    {
        $updateItemPage = PageObjectFactory::createPage($this->utilityContext, UpdateItemPage::PAGE_NAME);
        $updateItemPage->updateForm->selectFieldDefinition($fieldName);
        $updateItemPage->updateForm->clickAddFieldDefinition();
        $updateItemPage->updateForm->verifyNewFieldDefinitionFormExists(sprintf('New FieldDefinition (%s)', $fieldName));
    }

    /**
     * @When I set :field in :containerName to :value
     */
    public function iSetFieldInContainer(string $field, string $containerName, string $value): void
    {
        $updateItemPage = PageObjectFactory::createPage($this->utilityContext, UpdateItemPage::PAGE_NAME);
        $updateItemPage->updateForm->expandFieldDefinition(sprintf('New FieldDefinition (%s)', $containerName));
        $updateItemPage->updateForm->fillFieldWithValue($field, $value, sprintf('New FieldDefinition (%s)', $containerName));
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
