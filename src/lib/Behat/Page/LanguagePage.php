<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\Page;

use Behat\Mink\Session;
use eZ\Publish\API\Repository\Repository;
use Ibexa\AdminUi\Behat\Component\Dialog;
use Ibexa\AdminUi\Behat\Component\Table\TableBuilder;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\Behat\Browser\Page\Page;
use Ibexa\Behat\Browser\Routing\Router;
use PHPUnit\Framework\Assert;

class LanguagePage extends Page
{
    /** @var string */
    private $expectedLanguageName;

    /** @var \Ibexa\AdminUi\Behat\Component\Table\Table */
    private $table;

    /** @var \Ibexa\AdminUi\Behat\Component\Dialog */
    private $dialog;

    /** @var int */
    private $expectedLanguageId;

    /** @var \eZ\Publish\API\Repository\Repository */
    private $repository;

    public function __construct(Session $session, Router $router, TableBuilder $tableBuilder, Dialog $dialog, Repository $repository)
    {
        parent::__construct($session, $router);
        $this->table = $tableBuilder->newTable()->build();
        $this->dialog = $dialog;
        $this->repository = $repository;
    }

    public function delete()
    {
        $this->getHTMLPage()->find($this->getLocator('deleteButton'))->click();
        $this->dialog->verifyIsLoaded();
        $this->dialog->confirm();
    }

    public function hasProperties($data): bool
    {
        $hasExpectedEnabledFieldValue = true;
        if (array_key_exists('Enabled', $data)) {
            // Table does not handle returning non-string values
            $hasEnabledField = $this->getHTMLPage()->find($this->getLocator('enabledField'))->getValue() === 'on';
            $shouldHaveEnabledField = 'true' === $data['Enabled'];
            $hasExpectedEnabledFieldValue = $hasEnabledField === $shouldHaveEnabledField;
            unset($data['Enabled']);
        }

        return $hasExpectedEnabledFieldValue && $this->table->hasElement($data);
    }

    public function edit()
    {
        $this->getHTMLPage()->find($this->getLocator('editButton'))->click();
    }

    public function getName(): string
    {
        return 'Language';
    }

    public function setExpectedLanguageName(string $languageName)
    {
        $this->expectedLanguageName = $languageName;

        $languages = $this->repository->sudo(static function (Repository $repository) {
            return $repository->getContentLanguageService()->loadLanguages();
        });

        foreach ($languages as $language) {
            if ($language->name === $languageName) {
                $this->expectedLanguageId = $language->id;

                return;
            }
        }
    }

    public function verifyIsLoaded(): void
    {
        Assert::assertEquals(
            sprintf('Language "%s"', $this->expectedLanguageName),
            $this->getHTMLPage()->find($this->getLocator('pageTitle'))->getText()
        );
    }

    protected function getRoute(): string
    {
        return sprintf('/language/view/%d', $this->expectedLanguageId);
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('pageTitle', '.ez-page-title h1'),
            new VisibleCSSLocator('deleteButton', 'button[data-bs-original-title="Delete language"]'),
            new VisibleCSSLocator('editButton', '[data-bs-original-title="Edit"]'),
            new VisibleCSSLocator('enabledField', 'input[data-bs-original-title="Enabled"]'),
        ];
    }
}
