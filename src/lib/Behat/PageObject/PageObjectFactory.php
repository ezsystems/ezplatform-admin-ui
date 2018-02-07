<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageObject;

use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;
use EzSystems\StudioUIBundle\Tests\Behat\PageObject\LandingPageEditorPage;

class PageObjectFactory
{
    /**
     * Creates a Page object based on given Page Name.
     *
     * @param UtilityContext $context
     * @param string $pageName Name of the Page to creator
     * @param string $parameter additional parameter for constructor, e.g. name of item
     *
     * @return LoginPage|DashboardPage|ContentStructurePage|ContentTypeGroupsPage|UpdateItemPage|ContentTypePage to interact with
     */
    public static function createPage(UtilityContext $context, string $pageName, ?string ...$parameters): Page
    {
        switch ($pageName) {
            case LoginPage::PAGE_NAME:
                return new LoginPage($context);
            case DashboardPage::PAGE_NAME:
                return new DashboardPage($context);
            case ContentStructurePage::PAGE_NAME:
                return new ContentStructurePage($context);
            case ContentTypeGroupsPage::PAGE_NAME:
                return new ContentTypeGroupsPage($context);
            case UpdateItemPage::PAGE_NAME:
                return new UpdateItemPage($context);
            case ContentTypeGroupPage::PAGE_NAME:
                return new ContentTypeGroupPage($context, $parameters[0]);
            case ContentTypePage::PAGE_NAME:
                return new ContentTypePage($context, $parameters[0]);
            case LandingPageEditorPage::PAGE_NAME:
                return new LandingPageEditorPage($context);
            default:
                throw new \InvalidArgumentException(sprintf('Unrecognised page name: %s', $pageName));
        }
    }
}
