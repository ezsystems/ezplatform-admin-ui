<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\BrowserContext;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use EzSystems\Behat\Core\Behat\ArgumentParser;
use Ibexa\AdminUi\Behat\Component\DraftConflictDialog;
use Ibexa\AdminUi\Behat\Component\UniversalDiscoveryWidget;
use Ibexa\AdminUi\Behat\Page\ContentViewPage;
use PHPUnit\Framework\Assert;

class ContentViewContext implements Context
{
    private $argumentParser;

    /** @var \Ibexa\AdminUi\Behat\Page\ContentViewPage */
    private $contentViewPage;

    /** @var \Ibexa\AdminUi\Behat\Component\UniversalDiscoveryWidget */
    private $universalDiscoveryWidget;

    /** @var \Ibexa\AdminUi\Behat\Component\DraftConflictDialog */
    private $draftConflictDialog;

    public function __construct(
        ArgumentParser $argumentParser,
        ContentViewPage $contentViewPage,
        UniversalDiscoveryWidget $universalDiscoveryWidget,
        DraftConflictDialog $draftConflictDialog
    ) {
        $this->argumentParser = $argumentParser;
        $this->contentViewPage = $contentViewPage;
        $this->universalDiscoveryWidget = $universalDiscoveryWidget;
        $this->draftConflictDialog = $draftConflictDialog;
    }

    /**
     * @Given I start creating a new Content :contentType
     */
    public function startCreatingContent(string $contentType): void
    {
        $this->contentViewPage->startCreatingContent($contentType);
    }

    /**
     * @Given I switch to :tab tab in Content structure
     */
    public function switchTab(string $tabName)
    {
        $this->contentViewPage->switchToTab($tabName);
    }

    /**
     * @Given I add a new Location under :newLocationPath
     */
    public function iAddNewLocation(string $newLocationPath): void
    {
        $newLocationPath = $this->argumentParser->replaceRootKeyword($newLocationPath);
        $this->contentViewPage->addLocation($newLocationPath);
    }

    /**
     * @Given I start creating a new User
     */
    public function startCreatingUser(): void
    {
        $this->contentViewPage->startCreatingUser();
    }

    /**
     * @Given I start editing the current content
     * @Given I start editing the current content in :language language
     */
    public function startEditingContent(string $language = null): void
    {
        $this->contentViewPage->editContent($language);
    }

    /**
     * @Then there's a :itemName :itemType on Subitems list
     */
    public function verifyThereIsItemInSubItemList(string $itemName, string $itemType): void
    {
        $this->contentViewPage->verifyIsLoaded();
        Assert::assertTrue($this->contentViewPage->isChildElementPresent(['Name' => $itemName, 'Content type' => $itemType]));
    }

    /**
     * @Then there's no :itemName :itemType on Subitems list
     */
    public function verifyThereIsNoItemInSubItemListInRoot(string $itemName, string $itemType): void
    {
        $this->contentViewPage->verifyIsLoaded();
        Assert::assertFalse($this->contentViewPage->isChildElementPresent(['Name' => $itemName, 'Content type' => $itemType]));
    }

    /**
     * @Then content attributes equal
     */
    public function contentAttributesEqual(TableNode $parameters): void
    {
        foreach ($parameters->getHash() as $fieldData) {
            $fieldLabel = $fieldData['label'];
            $fieldTypeIdentifier = $fieldData['fieldTypeIdentifier'] ?? null;
            $expectedFieldValues = $fieldData;
            $this->contentViewPage->verifyFieldHasValues($fieldLabel, $expectedFieldValues, $fieldTypeIdentifier);
        }
    }

    /**
     * @When I start creating new draft from draft conflict modal
     */
    public function startCreatingNewDraftFromDraftConflictModal(): void
    {
        $this->draftConflictDialog->verifyIsLoaded();
        $this->draftConflictDialog->createNewDraft();
    }

    /**
     * @When I start editing draft with version number :versionNumber from draft conflict modal
     */
    public function startEditingDraftFromDraftConflictModal(string $versionNumber): void
    {
        $this->draftConflictDialog->verifyIsLoaded();
        $this->draftConflictDialog->edit($versionNumber);
    }

    /**
     * @When I send content to trash
     */
    public function iSendContentToTrash(): void
    {
        $this->contentViewPage->sendToTrash();
    }
}
