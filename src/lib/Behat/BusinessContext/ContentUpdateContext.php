<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\BusinessContext;

use Behat\Gherkin\Node\TableNode;
use EzSystems\Behat\Core\Environment\EnvironmentConstants;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields\NonEditableField;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\ContentUpdateItemPage;
use EzSystems\Behat\Browser\Factory\PageObjectFactory;
use PHPUnit\Framework\Assert;

class ContentUpdateContext extends BusinessContext
{
    /**
     * @When I set content fields
     */
    public function iSetFields(TableNode $table): void
    {
        $updateItemPage = PageObjectFactory::createPage($this->browserContext, ContentUpdateItemPage::PAGE_NAME, '');
        $hash = $table->getHash();
        foreach ($hash as $row) {
            $values = $this->filterOutNonEmptyValues($row);
            $updateItemPage->contentUpdateForm->fillFieldWithValue($row['label'], $values);
        }
    }

    /**
     * @Given the :fieldName field is noneditable
     */
    public function verifyFieldIsNotEditable(string $fieldName): void
    {
        $updateItemPage = PageObjectFactory::createPage($this->browserContext, ContentUpdateItemPage::PAGE_NAME, '');
        $field = $updateItemPage->contentUpdateForm->getField($fieldName);
        Assert::assertEquals(NonEditableField::EXPECTED_NON_EDITABLE_TEXT, $field->getValue()[0]);
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
        $updateItemPage = PageObjectFactory::createPage($this->browserContext, ContentUpdateItemPage::PAGE_NAME, '');
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
        $updateItemPage = PageObjectFactory::createPage($this->browserContext, ContentUpdateItemPage::PAGE_NAME, '');
        $updateItemPage->contentUpdateForm->closeUpdateForm();
    }

    /**
     * @When I set article main content field to :intro
     */
    public function iSetArticleMainContentField(string $intro): void
    {
        $updateItemPage = PageObjectFactory::createPage($this->browserContext, ContentUpdateItemPage::PAGE_NAME, '');
        $fieldName = EnvironmentConstants::get('ARTICLE_MAIN_FIELD_NAME');
        $updateItemPage->contentUpdateForm->fillFieldWithValue($fieldName, ['value' => $intro]);
    }

    /**
     * @Then article main content field is set to :intro
     */
    public function verifyArticleMainContentFieldIsSet(string $intro): void
    {
        $updateItemPage = PageObjectFactory::createPage($this->browserContext, ContentUpdateItemPage::PAGE_NAME, '');
        $fieldName = EnvironmentConstants::get('ARTICLE_MAIN_FIELD_NAME');
        $updateItemPage->contentUpdateForm->verifyFieldHasValue(['label' => $fieldName, 'value' => $intro]);
    }
}
