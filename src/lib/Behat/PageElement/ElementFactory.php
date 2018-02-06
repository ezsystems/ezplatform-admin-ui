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
     * @return AdminList|Dialog|RightMenu|UpperMenu|UpdateForm|Breadcrumb|Notification Element to interact with
     */
    public static function createElement(UtilityContext $context, string $elementName, ?string ...$parameters): Element
    {
        switch ($elementName) {
            case AdminList::ELEMENT_NAME:
                return new AdminList($context, $parameters[0]);
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
            default:
                throw new \InvalidArgumentException(sprintf('Unrecognised element name: %s', $elementName));
        }
    }
}
