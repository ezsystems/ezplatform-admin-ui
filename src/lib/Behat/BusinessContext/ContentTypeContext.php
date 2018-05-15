<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\BusinessContext;

use Behat\Gherkin\Node\TableNode;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\ContentTypePage;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\PageObjectFactory;
use PHPUnit\Framework\Assert;

class ContentTypeContext extends BusinessContext
{
    private $contentTypeTableHeaders = ['Name', 'Identifier', 'Description'];

    /**
     * @Then Content Type has proper Global properties
     */
    public function contentTypeHasProperGlobalProperties(TableNode $table): void
    {
        $hash = $table->getHash();
        $contentTypePage = PageObjectFactory::createPage($this->utilityContext, ContentTypePage::PAGE_NAME, $hash[0]['value']);
        foreach ($hash as $row) {
            if (in_array($row['label'], $this->contentTypeTableHeaders)) {
                $actualValue = $contentTypePage->contentTypeAdminList->table->getTableCellValue($row['label']);
            } else {
                $actualValue = $contentTypePage->globalPropertiesTable->getTableCellValue($row['label']);
            }

            Assert::assertEquals(
                $row['value'],
                $actualValue,
                sprintf('Content Type has wrong %s - actual: %s, expected: %s.', $row['label'], $actualValue, $row['value'])
            );
        }
    }

    /**
     * @Then Content Type :contentTypeName has field :fieldName of type :fieldType
     */
    public function contentTypeHasField(string $contentTypeName, string $fieldName, string $fieldType): void
    {
        $actualFieldType = PageObjectFactory::createPage($this->utilityContext, ContentTypePage::PAGE_NAME, $contentTypeName)
            ->fieldsAdminList->table->getTableCellValue('Type', $fieldName);

        if ($actualFieldType !== $fieldType) {
            throw new \Exception(
                sprintf(
                    'Content Type field %s has wrong type - actual: %s, expected: %s.',
                    $fieldName,
                    $actualFieldType,
                    $fieldType
                ));
        }
    }

    /**
     * @Then Content Type :contentTypeName has proper fields
     */
    public function contentTypeHasProperFields(string $contentTypeName, TableNode $table): void
    {
        $hash = $table->getHash();
        foreach ($hash as $row) {
            $actualFieldType = PageObjectFactory::createPage($this->utilityContext, ContentTypePage::PAGE_NAME, $contentTypeName)
                ->contentAdminList->table->getTableCellValue('Type', $row['fieldName']);

            Assert::assertEquals(
                $row['fieldType'],
                $actualFieldType,
                sprintf('Content Type field %s has wrong type', $row['fieldName'])
            );
        }
    }
}
