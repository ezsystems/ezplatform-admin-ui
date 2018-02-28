<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageObject;

use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;

class DashboardPage extends Page
{
    /** @var string Name by which Page is recognised */
    public const PAGE_NAME = 'Dashboard';

    public function __construct(UtilityContext $context)
    {
        parent::__construct($context);
        $this->route = '/admin/dashboard';
        $this->fields = ['meSection' => '.card-body'];
        $this->pageTitle = 'My dashboard';
        $this->pageTitleLocator = '.ez-header h1';
    }

    /**
     * Verifies that the Dashboard has the "Me" section.
     */
    public function verifyElements(): void
    {
        $this->context->waitUntilElementIsVisible($this->fields['meSection']);
    }
}
