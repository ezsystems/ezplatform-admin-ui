<?php


namespace Ibexa\AdminUi\Behat\Page;


use Behat\Mink\Session;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\Behat\Browser\Page\Page;
use Ibexa\Behat\Browser\Routing\Router;
use PHPUnit\Framework\Assert;

class SegmentsEditPage extends Page
{
    public function __construct(Session $session, Router $router)
    {
        parent::__construct($session, $router);
    }

    public function getName(): string
    {

    }

    public function verifyIsLoaded(): void
    {
    }

    protected function getRoute(): string
    {
        return '/segmentation/group/view';
    }

    protected function specifyLocators(): array
    {
        return [

        ];
    }
}