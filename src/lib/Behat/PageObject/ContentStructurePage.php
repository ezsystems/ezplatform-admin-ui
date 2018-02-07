<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageObject;

use EzSystems\EzPlatformAdminUi\Behat\PageElement\RightMenu;
use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;

class ContentStructurePage extends Page
{
    /** @var string Route under which the Page is available */
    protected $route = '/admin/content/location';

    /** @var string Name by which Page is recognised */
    public const PAGE_NAME = 'ContentStructure';

    /** @var RightMenu Element representing the right menu */
    protected $rightMenu;

    public function __construct(UtilityContext $context)
    {
        parent::__construct($context);
        $this->rightMenu = new RightMenu($this->context);
    }

    /**
     * Clicks "Create" and selects Content Type in displayed search.
     *
     * @param $contentType
     */
    public function startCreatingContent(string $contentType): void
    {
        $this->rightMenu->clickButton('Create');
        $this->context->getElementByText($contentType, '.form-check-label')->click();
    }

    public function verifyElements(): void
    {
        // TODO: Implement verifyElements() method.
    }
}
