<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\AdminUi\Behat\Page;

use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Behat\Mink\Session;
use Ibexa\Behat\Browser\Routing\Router;
use Ibexa\AdminUi\Behat\Component\RightMenu;
use Traversable;

class UserUpdatePage extends ContentUpdateItemPage
{
    public function __construct(Session $session, Router $router, RightMenu $rightMenu, Traversable $fieldTypeComponents)
    {
        parent::__construct($session, $router, $rightMenu, $fieldTypeComponents);
        $this->locators->replace(
            new VisibleCSSLocator(
                'formElement',
                '[name=ezplatform_content_forms_user_create],[name=ezplatform_content_forms_user_update]'
            )
        );
    }
}
