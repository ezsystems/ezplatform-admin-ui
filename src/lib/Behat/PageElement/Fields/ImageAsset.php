<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields;

use EzSystems\Behat\Browser\Factory\ElementFactory;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Notification;
use PHPUnit\Framework\Assert;

class ImageAsset extends Image
{
    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'Image Asset';

    private const IMAGE_ASSET_NOTIFICATION_MESSAGE = 'The image has been published and can now be reused';

    public function setValue(array $parameters): void
    {
        $notification = ElementFactory::createElement($this->context, Notification::ELEMENT_NAME);

        // close notification about new draft created successfully if it's still visible
        if ($notification->isVisible()) {
            $notification->verifyAlertSuccess();
            $notification->closeAlert();
        }

        parent::setValue($parameters);

        $imageAssetNotification = ElementFactory::createElement($this->context, Notification::ELEMENT_NAME);
        $imageAssetNotification->verifyAlertSuccess();
        Assert::assertEquals(self::IMAGE_ASSET_NOTIFICATION_MESSAGE, $imageAssetNotification->getMessage());
    }
}
