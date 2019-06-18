<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\BusinessContext;

use EzSystems\EzPlatformAdminUi\Behat\PageObject\ContentPreviewPage;
use EzSystems\Behat\Browser\Factory\PageObjectFactory;

class ContentPreviewContext extends BusinessContext
{
    /**
     * @When I go to :viewName view in :contentName preview
     */
    public function iGoToPreview(string $viewName, string $contentName): void
    {
        $previewPage = PageObjectFactory::createPage($this->browserContext, ContentPreviewPage::PAGE_NAME, $contentName);
        $previewPage->verifyIsLoaded();
        $previewPage->previewNav->goToView($viewName);
    }

    /**
     * @When I go back from content :contentName preview
     */
    public function iGoToBackFromPreview(string $contentName): void
    {
        $previewPage = PageObjectFactory::createPage($this->browserContext, ContentPreviewPage::PAGE_NAME, $contentName);
        $previewPage->verifyIsLoaded();
        $previewPage->previewNav->goBackToEditView();
    }
}
