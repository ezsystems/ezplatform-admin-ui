<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageObject;

use EzSystems\Behat\Browser\Context\BrowserContext;
use EzSystems\Behat\Browser\Factory\ElementFactory;
use EzSystems\Behat\Browser\Page\Page;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\PreviewNav;

class ContentPreviewPage extends Page
{
    /** @var string Name by which Page is recognised */
    public const PAGE_NAME = 'ContentPreview';

    public $previewNav;

    public function __construct(BrowserContext $context, string $contentName)
    {
        parent::__construct($context);
        $this->route = '/admin/content';
        $this->pageTitle = 'Previewing: ' . $contentName;
        $this->pageTitleLocator = '.ez-preview__nav .ez-preview__item--description';
        $this->previewNav = ElementFactory::createElement($context, PreviewNav::ELEMENT_NAME);
    }

    public function verifyElements(): void
    {
        $this->previewNav->verifyVisibility();
    }
}
