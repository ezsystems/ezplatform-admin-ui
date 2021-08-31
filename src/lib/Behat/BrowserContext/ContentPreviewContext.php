<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\BrowserContext;

use Behat\Behat\Context\Context;
use Ibexa\AdminUi\Behat\Page\ContentPreviewPage;

class ContentPreviewContext implements Context
{
    /** @var \Ibexa\AdminUi\Behat\Page\ContentPreviewPage */
    private $contentPreviewPage;

    public function __construct(ContentPreviewPage $contentPreviewPage)
    {
        $this->contentPreviewPage = $contentPreviewPage;
    }

    /**
     * @When I go to :viewName preview
     */
    public function iGoToPreview(string $viewName): void
    {
        $this->contentPreviewPage->verifyIsLoaded();
        $this->contentPreviewPage->goToView($viewName);
    }

    /**
     * @When I go back from content preview
     */
    public function iGoToBackFromPreview(): void
    {
        $this->contentPreviewPage->verifyIsLoaded();
        $this->contentPreviewPage->goBackToEditView();
    }
}
