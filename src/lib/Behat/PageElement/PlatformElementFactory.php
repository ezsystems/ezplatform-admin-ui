<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields\Authors;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields\Checkbox;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields\ContentRelationMultiple;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields\ContentRelationSingle;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields\Country;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields\Date;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields\DateAndTime;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields\DefaultFieldElement;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields\EmailAddress;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields\File;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields\FloatField;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields\Image;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields\Integer;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields\ISBN;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields\Keywords;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields\MapLocation;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields\Media;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields\RichText;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields\Selection;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields\TextBlock;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields\TextLine;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields\Time;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields\URL;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields\UserAccount;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\ContentRelationTable;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\DashboardTable;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\DoubleHeaderTable;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\DraftConflictTable;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\IconLinkedListTable;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\LinkedListTable;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\SimpleListTable;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\SimpleTable;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\SubItemsTable;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\SystemInfoTable;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\TrashTable;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\VerticalOrientedTable;

class PlatformElementFactory extends ElementFactory
{
    /**
     * @param UtilityContext $context
     * @param string $elementName
     * @param null[]|string[] ...$parameters
     *
     * @return AdminList|AdminUpdateForm|Breadcrumb|ContentField|ContentRelationTable|ContentTypePicker|ContentUpdateForm|DashboardTable|DateAndTimePopup|DefaultFieldElement|Dialog|DoubleHeaderTable|DraftConflictDialog|DraftConflictDialog|IconLinkedListTable|LanguagePicker|LeftMenu|LinkedListTable|NavLinkTabs|Notification|PreviewNav|RightMenu|SimpleListTable|SimpleTable|SubItemsTable|SystemInfoTable|TrashTable|UniversalDiscoveryWidget|UpperMenu|VerticalOrientedTable
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
            case DraftConflictTable::ELEMENT_NAME:
                return new DraftConflictTable($context, $parameters[0]);
            case LinkedListTable::ELEMENT_NAME:
                return new LinkedListTable($context, $parameters[0]);
            case IconLinkedListTable::ELEMENT_NAME:
                return new IconLinkedListTable($context, $parameters[0]);
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
            case TextBlock::ELEMENT_NAME:
                return new TextBlock($context, $parameters[0], $parameters[1]);
            case Authors::ELEMENT_NAME:
                return new Authors($context, $parameters[0], $parameters[1]);
            case Checkbox::ELEMENT_NAME:
                return new Checkbox($context, $parameters[0], $parameters[1]);
            case Country::ELEMENT_NAME:
                return new Country($context, $parameters[0], $parameters[1]);
            case Date::ELEMENT_NAME:
                return new Date($context, $parameters[0], $parameters[1]);
            case DateAndTime::ELEMENT_NAME:
                return new DateAndTime($context, $parameters[0], $parameters[1]);
            case EmailAddress::ELEMENT_NAME:
                return new EmailAddress($context, $parameters[0], $parameters[1]);
            case FloatField::ELEMENT_NAME:
                return new FloatField($context, $parameters[0], $parameters[1]);
            case Integer::ELEMENT_NAME:
                return new Integer($context, $parameters[0], $parameters[1]);
            case ISBN::ELEMENT_NAME:
                return new ISBN($context, $parameters[0], $parameters[1]);
            case Keywords::ELEMENT_NAME:
                return new Keywords($context, $parameters[0], $parameters[1]);
            case MapLocation::ELEMENT_NAME:
                return new MapLocation($context, $parameters[0], $parameters[1]);
            case Selection::ELEMENT_NAME:
                return new Selection($context, $parameters[0], $parameters[1]);
            case Time::ELEMENT_NAME:
                return new Time($context, $parameters[0], $parameters[1]);
            case URL::ELEMENT_NAME:
                return new URL($context, $parameters[0], $parameters[1]);
            case Media::ELEMENT_NAME:
                return new Media($context, $parameters[0], $parameters[1]);
            case Image::ELEMENT_NAME:
                return new Image($context, $parameters[0], $parameters[1]);
            case File::ELEMENT_NAME:
                return new File($context, $parameters[0], $parameters[1]);
            case ContentRelationSingle::ELEMENT_NAME:
                return new ContentRelationSingle($context, $parameters[0], $parameters[1]);
            case ContentRelationMultiple::ELEMENT_NAME:
                return new ContentRelationMultiple($context, $parameters[0], $parameters[1]);
            case ContentRelationTable::ELEMENT_NAME:
                return new ContentRelationTable($context, $parameters[0]);
            case UserAccount::ELEMENT_NAME:
                return new UserAccount($context, $parameters[0], $parameters[1]);
            case DefaultFieldElement::ELEMENT_NAME:
                return new DefaultFieldElement($context, $parameters[0], $parameters[1]);
            case LanguagePicker::ELEMENT_NAME:
                return new LanguagePicker($context);
            case DateAndTimePopup::ELEMENT_NAME:
                if (!array_key_exists(0, $parameters)) {
                    return new DateAndTimePopup($context);
                }

                if (!array_key_exists(1, $parameters)) {
                    return new DateAndTimePopup($context, $parameters[0]);
                }

                return new DateAndTimePopup($context, $parameters[0], $parameters[1]);
            case ContentTypePicker::ELEMENT_NAME:
                return new ContentTypePicker($context);
            case UniversalDiscoveryWidget::ELEMENT_NAME:
                return new UniversalDiscoveryWidget($context);
            case Pagination::ELEMENT_NAME:
                return new Pagination($context);
            default:
                throw new \InvalidArgumentException(sprintf('Unrecognized element name: %s', $elementName));
        }
    }

    public static function getPreviewType(string $elementName)
    {
        switch ($elementName) {
            default:
                throw new \InvalidArgumentException(sprintf('Unrecognized preview for element name: %s', $elementName));
        }
    }
}
