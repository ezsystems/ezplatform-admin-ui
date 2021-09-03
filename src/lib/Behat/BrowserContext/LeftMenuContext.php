<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\BrowserContext;

use Behat\Behat\Context\Context;
use Ibexa\AdminUi\Behat\Component\LeftMenu;

class LeftMenuContext implements Context
{
    /** @var \Ibexa\AdminUi\Behat\Component\LeftMenu */
    private $leftMenu;

    public function __construct(LeftMenu $leftMenu)
    {
        $this->leftMenu = $leftMenu;
    }

    /**
     * @When I click on the left menu bar button :buttonName
     */
    public function iClickLeftMenuBarButton(string $buttonName): void
    {
        $this->leftMenu->clickButton($buttonName);
    }
}
