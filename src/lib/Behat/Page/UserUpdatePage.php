<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\AdminUi\Behat\Page;

use Behat\Mink\Session;
use Ibexa\AdminUi\Behat\Component\ContentActionsMenu;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\Behat\Browser\Routing\Router;
use Traversable;

class UserUpdatePage extends ContentUpdateItemPage
{
    public function __construct(Session $session, Router $router, ContentActionsMenu $contentActionsMenu, Traversable $fieldTypeComponents)
    {
        parent::__construct($session, $router, $contentActionsMenu, $fieldTypeComponents);
        $this->locators->replace(
            new VisibleCSSLocator(
                'formElement',
                '[name=ezplatform_content_forms_user_create],[name=ezplatform_content_forms_user_update]'
            )
        );
    }
}
