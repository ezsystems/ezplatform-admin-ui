<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\BusinessContext;

use EzSystems\EzPlatformAdminUi\Behat\PageObject\ContentStructurePage;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\PageObjectFactory;
use PHPUnit\Framework\Assert;

class ContentViewContext extends BusinessContext
{
    /**
     * @Given I start creating a new Landing Page :name
     */
    public function startCreatingNewLandingPage($name)
    {
        $contentStructurePage = PageObjectFactory::createPage($this->utilityContext, ContentStructurePage::PAGE_NAME);
        $contentStructurePage->createLandingPage($name, 'Test Desc');
    }

    /**
     * @Then I (should) see :title title/topic
     */
    public function iSeeTitle($title)
    {
        $contentStructurePage = PageObjectFactory::createPage($this->utilityContext, ContentStructurePage::PAGE_NAME);
        Assert::assertEquals($title, $contentStructurePage->getPageHeaderTitle());
    }
}
