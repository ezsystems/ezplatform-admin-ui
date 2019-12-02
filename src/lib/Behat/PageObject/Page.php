<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageObject;

use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;
use PHPUnit\Framework\Assert;

abstract class Page
{
    protected $defaultTimeout = 50;

    /** @var string Route under which the Page is available */
    protected $route;

    /** @var string SiteAccess name */
    public $siteAccess;

    /** @var string title that we see directly below upper menu */
    protected $pageTitle;

    /** @var UtilityContext context for interactions with the page */
    protected $context;

    /** @var string locator for page title */
    protected $pageTitleLocator;

    // @var UpperMenu
    public $upperMenu;

    public function __construct(UtilityContext $context)
    {
        $this->context = $context;
    }

    /**
     * Makes sure that the page is loaded.
     */
    public function verifyIsLoaded(): void
    {
        $this->verifyRoute();
        $this->verifyElements();
        $this->verifyTitle();
    }

    /**
     * Opens the page in Browser.
     *
     * @param bool $verifyIfLoaded Page content will be verified
     */
    public function open(bool $verifyIfLoaded = true): void
    {
        if (isset($this->siteAccess)) {
            $url = $this->context->reverseMatchRoute($this->siteAccess, $this->route);
        } else {
            $url = $this->route;
        }

        $this->context->visit($url);

        if ($verifyIfLoaded) {
            $this->verifyIsLoaded();
        }
    }

    /**
     * Verifies that Page is available under correct route.
     */
    public function verifyRoute(): void
    {
        $this->context->waitUntil($this->defaultTimeout, function () {
            return false !== strpos($this->context->getSession()->getCurrentUrl(), $this->route);
        });
    }

    public function verifyTitle(): void
    {
        Assert::assertEquals(
            $this->pageTitle,
            $this->getPageTitle(),
            'Wrong page title.'
        );
    }

    /**
     * Verifies that expected elements are present.
     */
    abstract public function verifyElements(): void;

    /**
     * Gets the header text displayed in AdminUI.
     *
     * @return string
     */
    public function getPageTitle(): string
    {
        return $this->context->findElement($this->pageTitleLocator)->getText();
    }
}
