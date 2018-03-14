<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;

class ElementFactory
{
    /**
     * Creates a Element object based on given Element Name.
     *
     * @param UtilityContext $context
     * @param string $elementName Name of the Element to creator
     *
     * @return AdminList|Dialog|RightMenu|UpperMenu|UpdateForm|Breadcrumb|Notification|NavLinkTabs|UniversalDiscoveryWidget Element to interact with
     */
    public static function createElement(UtilityContext $context, string $elementName, ?string ...$parameters): Element
    {
        switch ($elementName) {
            case AdminList::ELEMENT_NAME:
                if (array_key_exists(2, $parameters)) {
                    return new AdminList($context, $parameters[0], $parameters[1], $parameters[2]);
                }

                return new AdminList($context, $parameters[0], $parameters[1]);
            case Dialog::ELEMENT_NAME:
                return new Dialog($context);
            case RightMenu::ELEMENT_NAME:
                return new RightMenu($context);
            case UpperMenu::ELEMENT_NAME:
                return new UpperMenu($context);
            case UpdateForm::ELEMENT_NAME:
                return new UpdateForm($context);
            case Breadcrumb::ELEMENT_NAME:
                return new Breadcrumb($context);
            case Notification::ELEMENT_NAME:
                return new Notification($context);
            case SimpleTable::ELEMENT_NAME:
                return new SimpleTable($context, $parameters[0]);
            case SimpleListTable::ELEMENT_NAME:
                return new SimpleListTable($context, $parameters[0]);
            case LinkedListTable::ELEMENT_NAME:
                return new LinkedListTable($context, $parameters[0]);
            case VerticalOrientedTable::ELEMENT_NAME:
                return new VerticalOrientedTable($context, $parameters[0]);
            case DoubleHeaderTable::ELEMENT_NAME:
                return new DoubleHeaderTable($context, $parameters[0]);
            case NavLinkTabs::ELEMENT_NAME:
                return new NavLinkTabs($context);
            case UniversalDiscoveryWidget::ELEMENT_NAME:
                return new UniversalDiscoveryWidget($context);
            default:
                throw new \InvalidArgumentException(sprintf('Unrecognized element name: %s', $elementName));
        }
    }
}
