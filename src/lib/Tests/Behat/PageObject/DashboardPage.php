<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Tests\Behat\PageObject;

class DashboardPage extends Page
{
    protected $fields = ['meSection' => '.card-body'];

    /** @var string Route under which the Page is available */
    protected $route = '/admin/dashboard';

    /** @var string Name by which Page is recognised */
    const PAGE_NAME = 'Dashboard';

    /**
     * Verifies that the Dashboard has the "Me" section.
     */
    public function verifyElements()
    {
        $this->context->waitUntilElementIsVisible($this->fields['meSection']);
    }
}
