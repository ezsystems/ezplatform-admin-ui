<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\BusinessContext;

use EzSystems\EzPlatformAdminUi\Behat\PageObject\PageObjectFactory;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\UpdateItemPage;

class UpdateContext extends BusinessContext
{
    /**
     * @When I set :field to :value
     * @When I set :field as empty
     */
    public function fillFieldWithValue(string $field, string $value = ''): void
    {
        PageObjectFactory::createPage($this->utilityContext, UpdateItemPage::PAGE_NAME)
            ->updateForm->fillFIeldWithValue($field, $value);
    }
}
