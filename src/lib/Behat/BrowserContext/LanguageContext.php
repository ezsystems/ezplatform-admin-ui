<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\BrowserContext;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Ibexa\AdminUi\Behat\Page\LanguagePage;
use Ibexa\AdminUi\Behat\Page\LanguagesPage;
use PHPUnit\Framework\Assert;

class LanguageContext implements Context
{
    /** @var \Ibexa\AdminUi\Behat\Page\LanguagesPage */
    private $languagesPage;

    /** @var \Ibexa\AdminUi\Behat\Page\LanguagePage */
    private $languagePage;

    public function __construct(LanguagePage $languagePage, LanguagesPage $languagesPage)
    {
        $this->languagePage = $languagePage;
        $this->languagesPage = $languagesPage;
    }

    /**
     * @When I create a new Language
     */
    public function createNewLanguage(): void
    {
        $this->languagesPage->create();
    }

    /**
     * @When  I delete the language
     */
    public function deleteLanguage(): void
    {
        $this->languagePage->delete();
    }

    /**
     * @Given there's no :languageName Language on Languages list
     */
    public function thereSNoLanguageOnLanguageList(string $languageName)
    {
        Assert::assertFalse($this->languagesPage->isLanguageOnTheList($languageName));
    }

    /**
     * @Given I delete Language :languageName
     */
    public function deleteLanguageNamed(string $languageName)
    {
        $this->languagesPage->deleteLanguage($languageName);
    }

    /**
     * @Given there's a :languageName Language on Languages list
     */
    public function thereALanguageOnLanguageList(string $languageName)
    {
        Assert::assertTrue($this->languagesPage->isLanguageOnTheList($languageName));
    }

    /**
     * @Then I should be on :languageName Language page
     */
    public function iShouldBeOnLanguagePage(string $languageName)
    {
        $this->languagePage->setExpectedLanguageName($languageName);
        $this->languagePage->verifyIsLoaded();
    }

    /**
     * @Then Language has proper attributes
     */
    public function languageHasProperAttributes(TableNode $languageData)
    {
        $expectedName = $languageData->getHash()[0]['Name'];
        $expectedCode = $languageData->getHash()[0]['Language code'];
        $expectedEnabledFlag = $languageData->getHash()[0]['Enabled'];

        Assert::assertTrue(
            $this->languagePage->hasProperties(['Name' => $expectedName, 'Language code' => $expectedCode, 'Enabled' => $expectedEnabledFlag])
        );
    }

    /**
     * @Then I edit :languageName from Languages list
     */
    public function editLanguageFromLanguagesList(string $languageName)
    {
        $this->languagesPage->editLanguage($languageName);
    }

    /**
     * @Then I open :languageName Language page in admin SiteAccess
     */
    public function openLanguagePage(string $languageName)
    {
        $this->languagePage->setExpectedLanguageName($languageName);
        $this->languagePage->open('admin');
        $this->languagePage->verifyIsLoaded();
    }

    /**
     * @Then I start editing the Language
     */
    public function editLanguage()
    {
        $this->languagePage->edit();
    }
}
