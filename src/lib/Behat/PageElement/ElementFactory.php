<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields\Authors;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields\DefaultFieldElement;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields\EzFieldElement;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields\RichText;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields\TextLine;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\DashboardTable;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\DoubleHeaderTable;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\LinkedListTable;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\SimpleListTable;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\SimpleTable;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\SubItemsTable;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\SystemInfoTable;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\TrashTable;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\VerticalOrientedTable;
use EzSystems\FlexWorkflow\Behat\PageElement\OldSendForReviewForm;
use EzSystems\StudioUIBundle\Tests\Behat\PageElement\ConfirmationPopup;

class ElementFactory
{
    /**
     * Creates a Element object based on given Element Name.
     *
     * @param UtilityContext $context
     * @param string $elementName Name of the Element to creator
     *
     * @return AdminList|Dialog|TrashTable|ContentField|DraftConflictDialog|DashboardTable|EzFieldElement|ContentUpdateForm|DefaultFieldElement|SimpleTable|DoubleHeaderTable|SubItemsTable|SimpleListTable|LinkedListTable|VerticalOrientedTable|PreviewNav|SystemInfoTable|RightMenu|UpperMenu|AdminUpdateForm|Breadcrumb|Notification|NavLinkTabs|UniversalDiscoveryWidget|LanguagePicker|OldSendForReviewForm|ConfirmationPopup Element to interact with
     */
    public static function createElement(UtilityContext $context, string $elementName, ?string ...$parameters): Element
    {
        switch ($elementName) {
            case AdminList::ELEMENT_NAME:
                if (!array_key_exists(2, $parameters)) {
                    $parameters[2] = null;
                }

                return new AdminList($context, $parameters[0], $parameters[1], $parameters[2]);
            case Dialog::ELEMENT_NAME:
                return new Dialog($context);
            case DraftConflictDialog::ELEMENT_NAME:
                return new DraftConflictDialog($context);
            case LeftMenu::ELEMENT_NAME:
                return new LeftMenu($context);
            case RightMenu::ELEMENT_NAME:
                return new RightMenu($context);
            case UpperMenu::ELEMENT_NAME:
                return new UpperMenu($context);
            case AdminUpdateForm::ELEMENT_NAME:
                return new AdminUpdateForm($context);
            case ContentField::ELEMENT_NAME:
                return new ContentField($context);
            case ContentUpdateForm::ELEMENT_NAME:
                return new ContentUpdateForm($context);
            case Breadcrumb::ELEMENT_NAME:
                return new Breadcrumb($context);
            case Notification::ELEMENT_NAME:
                return new Notification($context);
            case SimpleTable::ELEMENT_NAME:
                return new SimpleTable($context, $parameters[0]);
            case SimpleListTable::ELEMENT_NAME:
                return new SimpleListTable($context, $parameters[0]);
            case DashboardTable::ELEMENT_NAME:
                return new DashboardTable($context, $parameters[0]);
            case LinkedListTable::ELEMENT_NAME:
                return new LinkedListTable($context, $parameters[0]);
            case VerticalOrientedTable::ELEMENT_NAME:
                return new VerticalOrientedTable($context, $parameters[0]);
            case DoubleHeaderTable::ELEMENT_NAME:
                return new DoubleHeaderTable($context, $parameters[0]);
            case SystemInfoTable::ELEMENT_NAME:
                return new SystemInfoTable($context, $parameters[0]);
            case TrashTable::ELEMENT_NAME:
                return new TrashTable($context, $parameters[0]);
            case SubItemsTable::ELEMENT_NAME:
                return new SubItemsTable($context, $parameters[0]);
            case SubItemsList::ELEMENT_NAME:
                return new SubItemsList($context);
            case NavLinkTabs::ELEMENT_NAME:
                return new NavLinkTabs($context);
            case PreviewNav::ELEMENT_NAME:
                return new PreviewNav($context);
            case RichText::ELEMENT_NAME:
                return new RichText($context, $parameters[0], $parameters[1]);
            case TextLine::ELEMENT_NAME:
                return new TextLine($context, $parameters[0], $parameters[1]);
            case Authors::ELEMENT_NAME:
                return new Authors($context, $parameters[0], $parameters[1]);
            case DefaultFieldElement::ELEMENT_NAME:
                return new DefaultFieldElement($context, $parameters[0], $parameters[1]);
            case LanguagePicker::ELEMENT_NAME:
                return new LanguagePicker($context);
            case UniversalDiscoveryWidget::ELEMENT_NAME:
                return new UniversalDiscoveryWidget($context);
            case OldSendForReviewForm::ELEMENT_NAME:
                return new OldSendForReviewForm($context);
            case ConfirmationPopup::ELEMENT_NAME:
                return new ConfirmationPopup($context);
            default:
                throw new \InvalidArgumentException(sprintf('Unrecognized element name: %s', $elementName));
        }
    }
}
