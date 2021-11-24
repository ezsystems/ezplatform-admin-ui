<?php

namespace EzSystems\EzPlatformAdminUi\Behat\Page;

use Ibexa\Behat\Browser\Element\Condition\ElementsCountCondition;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\Behat\Browser\Page\Page;

class MyDashboardPage extends Page
{
    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->find(new VisibleCSSLocator('header', 'h1'))->assert()->textEquals("My dashboard1");

        $this->getHTMLPage()->find(new VisibleCSSLocator('header', 'h1'));
        $this->getHTMLPage()->find(new VisibleCSSLocator('header', 'h1'))->getText();
        $this->getHTMLPage()->find(new VisibleCSSLocator('header', 'h1'))->assert()->isVisible();
        $this->getHTMLPage()->find(new VisibleCSSLocator('header', 'h1'))->assert()->textEquals("My dashboard");
        $this->getHTMLPage()->find(new VisibleCSSLocator('header', 'h1'))->assert()->textEquals("My dashboard1");
        $this->getHTMLPage()->findAll(new VisibleCSSLocator('buttons', 'button'));
        $this->getHTMLPage()->findAll(new VisibleCSSLocator('buttons', 'button'))->count();
        $this->getHTMLPage()->findAll(new VisibleCSSLocator('buttons', 'button'))->mapBy(new ElementTextMapper());
        $this->getHTMLPage()->findAll(new VisibleCSSLocator('buttons', 'button'))->getByCriterion(new ElementTextCriterion('Create'));
        $this->getHTMLPage()->findAll(new VisibleCSSLocator('buttons', 'button'))->getByCriterion(new ElementTextCriterion('Create'))->mouseOver();
        $this->getHTMLPage()->findAll(new VisibleCSSLocator('buttons', 'button'))->getByCriterion(new ElementTextCriterion('Create'))->assert()->textEquals("Create");
        $this->getHTMLPage()->findAll(new VisibleCSSLocator('buttons', 'button'))->getByCriterion(new ElementTextCriterion('Create'))->assert()->textEquals("Create")->click();
        $this->getHTMLPage()->find(new VisibleCSSLocator('header', 'h1'))->assert()->textEquals("My dashboard");

        $this->getHTMLPage()->dragAndDrop();
        $this->getHTMLPage()->waitUntilCondition(new ElementsCountCondition($this->getHTMLPage(), $this->getLocator('blok-w-pb'), 3 ));
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('header', 'h1')
        ];
    }

    public function getName(): string
    {
        return "MyDashboard";
    }

    protected function getRoute(): string
    {
        return "/dashboard";
    }
}