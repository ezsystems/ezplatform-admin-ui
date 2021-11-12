<?php

namespace Ibexa\AdminUi\Behat\Page;

use EzSystems\Behat\Core\Debug\InteractiveDebuggerTrait;
use Ibexa\Behat\Browser\Element\Criterion\ElementTextCriterion;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\Behat\Browser\Page\Page;

class TestDashboardPage extends Page
{
    use InteractiveDebuggerTrait;

    public function verifyIsLoaded(): void
    {
        $this->enableDebugging();
        $this->getHTMLPage()->find($this->getLocator('header'))->assert()->textEquals('My dashboard');
        $this->getHTMLPage()
            ->setTimeout(5)
            ->findAll($this->getLocator('myContentHeaders'))
            ->getByCriterion(new ElementTextCriterion('Content'))
            ->assert()->textEquals('Content');
        $this->setInteractiveBreakpoint();
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('header', '.ez-dashboard__header h1'),
            new VisibleCSSLocator('myContentHeaders', '#ez-tab-list-dashboard-my li'),
        ];
    }

    public function getName(): string
    {
        return "Test Dashboard Page";
    }

    protected function getRoute(): string
    {
        return '/dashboard';
    }
}