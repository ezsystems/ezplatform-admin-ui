<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageObject;

use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\UpdateForm;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\ElementFactory;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\RightMenu;

class UpdateItemPage extends Page
{
    /** @var string Route under which the Page is available */
    protected $route = '/admin/login';

    /** @var string Name by which Page is recognised */
    public const PAGE_NAME = 'Admin Item Update';

    /**
     * @var UpdateForm
     */
    public $updateForm;

    /**
     * @var RightMenu
     */
    public $rightMenu;

    public function __construct(UtilityContext $context)
    {
        parent::__construct($context);
        $this->updateForm = ElementFactory::createElement($this->context, UpdateForm::ELEMENT_NAME);
        $this->rightMenu = ElementFactory::createElement($this->context, RightMenu::ELEMENT_NAME);
    }

    public function verifyElements(): void
    {
        $this->rightMenu->verifyVisibility();
        $this->updateForm->verifyVisibility();
    }
}
