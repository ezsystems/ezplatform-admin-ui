<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageObject;

use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;

class PlatformPageObjectFactory extends PageObjectFactory
{
    /**
     * @return LoginPage|TrashPage|ContentPreviewPage|ObjectStateGroupsPage|ObjectStateGroupPage|ObjectStatePage|SystemInfoPage|DashboardPage|ContentItemPage|ContentTypeGroupsPage|SectionPage|SectionsPage|ContentUpdateItemPage|AdminUpdateItemPage|ContentTypePage|LanguagesPage|LanguagePage|RolesPage|RolePage
     */
    public static function createPage(UtilityContext $context, string $pageName, ?string ...$parameters): Page
    {
        switch ($pageName) {
            case LoginPage::PAGE_NAME:
                return new LoginPage($context);
            case DashboardPage::PAGE_NAME:
                return new DashboardPage($context);
            case ContentItemPage::PAGE_NAME:
                return new ContentItemPage($context, $parameters[0]);
            case ContentTypeGroupsPage::PAGE_NAME:
                return new ContentTypeGroupsPage($context);
            case AdminUpdateItemPage::PAGE_NAME:
                return new AdminUpdateItemPage($context);
            case ContentUpdateItemPage::PAGE_NAME:
                return new ContentUpdateItemPage($context, $parameters[0]);
            case ContentTypeGroupPage::PAGE_NAME:
                return new ContentTypeGroupPage($context, $parameters[0]);
            case ContentTypePage::PAGE_NAME:
                return new ContentTypePage($context, $parameters[0]);
            case LanguagesPage::PAGE_NAME:
                return new LanguagesPage($context);
            case LanguagePage::PAGE_NAME:
                return new LanguagePage($context, $parameters[0]);
            case RolesPage::PAGE_NAME:
                return new RolesPage($context);
            case RolePage::PAGE_NAME:
                return new RolePage($context, $parameters[0]);
            case SystemInfoPage::PAGE_NAME:
                return new SystemInfoPage($context);
            case SectionsPage::PAGE_NAME:
                return new SectionsPage($context);
            case SectionPage::PAGE_NAME:
                return new SectionPage($context, $parameters[0]);
            case ObjectStateGroupsPage::PAGE_NAME:
                return new ObjectStateGroupsPage($context);
            case ObjectStateGroupPage::PAGE_NAME:
                return new ObjectStateGroupPage($context, $parameters[0]);
            case ObjectStatePage::PAGE_NAME:
                return new ObjectStatePage($context, $parameters[0]);
            case ContentPreviewPage::PAGE_NAME:
                return new ContentPreviewPage($context, $parameters[0]);
            case TrashPage::PAGE_NAME:
                return new TrashPage($context);
            default:
                throw new \InvalidArgumentException(sprintf('Unrecognised page name: %s', $pageName));
        }
    }
}
