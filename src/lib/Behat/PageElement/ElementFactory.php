<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

use EzSystems\DateBasedPublisher\Behat\PageElement\DateBasedPublisherPopup;
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
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields\EzFieldElement;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields\FloatField;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields\Integer;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields\ISBN;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields\Keywords;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields\MapLocation;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields\RichText;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields\Selection;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields\TextBlock;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields\TextLine;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields\Time;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields\URL;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields\UserAccount;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\DashboardTable;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\DoubleHeaderTable;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\LinkedListTable;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\SimpleListTable;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\SimpleTable;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\SubItemsTable;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\SystemInfoTable;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\TrashTable;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\VerticalOrientedTable;
use EzSystems\EzPlatformPageBuilder\Tests\Behat\PageElement\PageBuilderActionBar;
use EzSystems\EzPlatformPageBuilder\Tests\Behat\PageElement\PageBuilderCreatorPopup;
use EzSystems\EzPlatformPageBuilder\Tests\Behat\PageElement\PageEditorBlock;
use EzSystems\FlexWorkflow\Behat\PageElement\SendForReviewForm;

class ElementFactory
{
    /**
     * Creates a Element object based on given Element Name.
     *
     * @param UtilityContext $context
     * @param string $elementName Name of the Element to creator
     *
     * @return AdminList|Dialog|DateBasedPublisherPopup|DateAndTimePopup|TrashTable|ContentField|DraftConflictDialog|DashboardTable|EzFieldElement|ContentUpdateForm|DefaultFieldElement|SimpleTable|DoubleHeaderTable|SubItemsTable|SimpleListTable|LinkedListTable|VerticalOrientedTable|PreviewNav|SystemInfoTable|RightMenu|UpperMenu|AdminUpdateForm|Breadcrumb|Notification|NavLinkTabs|UniversalDiscoveryWidget|LanguagePicker|ContentTypePicker Element to interact with
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
            case ContentRelationSingle::ELEMENT_NAME:
                return new ContentRelationSingle($context, $parameters[0], $parameters[1]);
            case ContentRelationMultiple::ELEMENT_NAME:
                return new ContentRelationMultiple($context, $parameters[0], $parameters[1]);
            case UserAccount::ELEMENT_NAME:
                return new UserAccount($context, $parameters[0], $parameters[1]);
            case DefaultFieldElement::ELEMENT_NAME:
                return new DefaultFieldElement($context, $parameters[0], $parameters[1]);
            case LanguagePicker::ELEMENT_NAME:
                return new LanguagePicker($context);
            case DateAndTimePopup::ELEMENT_NAME:
                return new DateAndTimePopup($context);
            case ContentTypePicker::ELEMENT_NAME:
                return new ContentTypePicker($context);
            case UniversalDiscoveryWidget::ELEMENT_NAME:
                return new UniversalDiscoveryWidget($context);
            case DateBasedPublisherPopup::ELEMENT_NAME:
                return new DateBasedPublisherPopup($context);
            case SendForReviewForm::ELEMENT_NAME:
                return new SendForReviewForm($context);
            case PageBuilderCreatorPopup::ELEMENT_NAME:
                return new PageBuilderCreatorPopup($context);
            case PageBuilderActionBar::ELEMENT_NAME:
                return new PageBuilderActionBar($context);
            case 'Content List':
            case 'Banner':
            case 'Embed':
            case 'Gallery':
            case 'Keyword':
            case 'MA Form':
            case 'Places':
            case 'RSS':
            case 'Schedule':
            case 'Code':
            case 'Video':
            case 'Collection':
                return new PageEditorBlock($context);
            default:
                throw new \InvalidArgumentException(sprintf('Unrecognized element name: %s', $elementName));
        }
    }
}
