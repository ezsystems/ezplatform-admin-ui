<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\BrowserContext;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Ibexa\AdminUi\Behat\Component\UpperMenu;
use Ibexa\AdminUi\Behat\Component\UserNotificationPopup;
use PHPUnit\Framework\Assert;

class UserNotificationContext implements Context
{
    /** @var \Ibexa\AdminUi\Behat\Component\UpperMenu */
    private $upperMenu;

    /** @var \Ibexa\AdminUi\Behat\Component\UserNotificationPopup */
    private $userNotificationPopup;

    public function __construct(UpperMenu $upperMenu, UserNotificationPopup $userNotificationPopup)
    {
        $this->upperMenu = $upperMenu;
        $this->userNotificationPopup = $userNotificationPopup;
    }

    /**
     * @Given there is an unread notification for current user
     */
    public function thereIsNotificationForCurrentUser()
    {
        Assert::assertTrue($this->upperMenu->hasUnreadNotification());
    }

    /**
     * @Given I go to user notification with details:
     */
    public function iGoToUserNotificationWithDetails(TableNode $notificationDetails)
    {
        $type = $notificationDetails->getHash()[0]['Type'];
        $description = $notificationDetails->getHash()[0]['Description'];

        $this->userNotificationPopup->verifyIsLoaded();
        $this->userNotificationPopup->clickNotification($type, $description);
    }
}
