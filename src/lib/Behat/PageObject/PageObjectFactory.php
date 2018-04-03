<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageObject;

use EzSystems\FormBuilderBundle\Tests\Behat\PageObject\FormDeletionConfirmationPage;
use EzSystems\FormBuilderBundle\Tests\Behat\PageObject\FormDetailsPage;
use EzSystems\FormBuilderBundle\Tests\Behat\PageObject\FormManagerPage;
use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;
use EzSystems\StudioUIBundle\Tests\Behat\PageObject\LandingPageEditorPage;

class PageObjectFactory
{
    /**
     * Creates a Page object based on given Page Name.
     *
     * @param UtilityContext $context
     * @param string $pageName Name of the Page to creator
     * @param null[]|string[] $parameters additional parameters
     *
     * @return LoginPage|SystemInfoPage|DashboardPage|ContentStructurePage|ContentTypeGroupsPage|SectionPage|SectionsPage|UpdateItemPage|ContentTypePage|FormManagerPage|FormDetailsPage|FormDeletionConfirmationPage|LanguagesPage|LanguagePage|RolesPage|RolePage
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
            case FormManagerPage::PAGE_NAME:
                return new FormManagerPage($context);
            case FormDetailsPage::PAGE_NAME:
                return new FormDetailsPage($context, $parameters[0]);
            case FormDeletionConfirmationPage::PAGE_NAME:
                return new FormDeletionConfirmationPage($context);
            case LandingPageEditorPage::PAGE_NAME:
                return new LandingPageEditorPage($context);
            default:
                throw new \InvalidArgumentException(sprintf('Unrecognised page name: %s', $pageName));
        }
    }
}
