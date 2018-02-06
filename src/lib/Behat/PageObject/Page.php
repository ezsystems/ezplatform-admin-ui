<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageObject;

use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;

abstract class Page
{
    protected $defaultTimeout = 50;

    /** @var string Route under which the Page is available */
    protected $route;

    /** @var UtilityContext context for interactions with the page */
    protected $context;

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
    }

    /**
     * Opens the page in Browser.
     *
     * @param bool $verifyIfLoaded Page content will be verified
     */
    public function open(bool $verifyIfLoaded = true): void
    {
        $this->context->visit($this->route);

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

    /**
     * Verifies that expected elements are present.
     */
    abstract public function verifyElements(): void;

    /**
     * Gets the header text displayed in AdminUI.
     *
     * @return string
     */
    public function getPageHeaderTitle(): string
    {
        return $this->context->findElement('.ez-page-title')->getText();
    }
}
