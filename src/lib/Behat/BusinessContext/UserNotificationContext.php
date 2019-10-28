<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\BusinessContext;

use EzSystems\Behat\Browser\Factory\ElementFactory;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\UpperMenu;
use Behat\Gherkin\Node\TableNode;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\UserNotificationPopup;
use PHPUnit\Framework\Assert;

class UserNotificationContext extends BusinessContext
{
    /**
     * @Given there is an unread notification for current user
     */
    public function thereIsNotificationForCurrentUser()
    {
        $upperMenu = ElementFactory::createElement($this->browserContext, UpperMenu::ELEMENT_NAME);
        Assert::assertGreaterThan(0, $upperMenu->getNotificationsCount());
    }

    /**
     * @Given I go to user notification with details:
     */
    public function iGoToUserNotificationWithDetails(TableNode $notificationDetails)
    {
        $notificationsPopup = ElementFactory::createElement($this->browserContext, UserNotificationPopup::ELEMENT_NAME);
        $notificationsPopup->verifyVisibility();

        $type = $notificationDetails->getHash()[0]['Type'];
        $description = $notificationDetails->getHash()[0]['Description'];
        $notificationsPopup->clickItem($type, $description);
    }
}
