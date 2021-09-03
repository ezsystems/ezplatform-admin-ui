<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\BrowserContext;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Ibexa\AdminUi\Behat\Page\AdminUpdateItemPage;

class AdminUpdateContext implements Context
{
    /** @var \Ibexa\AdminUi\Behat\Page\AdminUpdateItemPage */
    private $adminUpdateItemPage;

    public function __construct(AdminUpdateItemPage $adminUpdateItemPage)
    {
        $this->adminUpdateItemPage = $adminUpdateItemPage;
    }

    /**
     * @When I set fields
     */
    public function iSetFields(TableNode $table): void
    {
        $this->adminUpdateItemPage->verifyIsLoaded();
        foreach ($table->getHash() as $row) {
            $this->adminUpdateItemPage->fillFieldWithValue($row['label'], $row['value']);
        }
    }
}
