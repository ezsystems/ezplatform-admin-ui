<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageObject;

use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\ElementFactory;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\PreviewNav;

class ContentPreviewPage extends Page
{
    /** @var string Name by which Page is recognised */
    public const PAGE_NAME = 'ContentPreview';

    public $previewNav;

    public function __construct(UtilityContext $context, string $contentName)
    {
        parent::__construct($context);
        $this->siteAccess = 'admin';
        $this->route = '/content';
        $this->pageTitle = 'Previewing: ' . $contentName;
        $this->pageTitleLocator = '.ez-preview__nav .ez-preview__item--description';
        $this->previewNav = ElementFactory::createElement($context, PreviewNav::ELEMENT_NAME);
    }

    public function verifyElements(): void
    {
        $this->previewNav->verifyVisibility();
    }
}
