<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\Component\Fields;

use Behat\Mink\Session;
use Ibexa\AdminUi\Behat\Component\Notification;
use Ibexa\AdminUi\Behat\Component\UniversalDiscoveryWidget;
use Ibexa\Behat\Browser\Element\Condition\ElementNotExistsCondition;
use Ibexa\Behat\Browser\FileUpload\FileUploadHelper;
use Ibexa\Behat\Browser\Locator\CSSLocatorBuilder;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use PHPUnit\Framework\Assert;

class ImageAsset extends Image
{
    /** @var \Ibexa\AdminUi\Behat\Component\UniversalDiscoveryWidget */
    private $universalDiscoveryWidget;

    /** @var \Ibexa\AdminUi\Behat\Component\Notification */
    private $notification;

    public function __construct(
        Session $session,
        FileUploadHelper $fileUploadHelper,
        UniversalDiscoveryWidget $universalDiscoveryWidget,
        Notification $notification
    ) {
        parent::__construct($session, $fileUploadHelper);
        $this->universalDiscoveryWidget = $universalDiscoveryWidget;
        $this->notification = $notification;
    }

    private const IMAGE_ASSET_NOTIFICATION_MESSAGE = 'The image has been published and can now be reused';

    public function setValue(array $parameters): void
    {
        // close notification about new draft created successfully if it's still visible
        if ($this->notification->isVisible()) {
            $this->notification->verifyAlertSuccess();
            $this->notification->closeAlert();
        }

        $fieldSelector = CSSLocatorBuilder::base($this->getLocator('fieldInput'))
            ->withParent($this->parentLocator)
            ->build();

        $this->getHTMLPage()->find($fieldSelector)->attachFile(
            $this->fileUploadHelper->getRemoteFileUploadPath($parameters['value'])
        );
        $this->getHTMLPage()->setTimeout(20)->waitUntilCondition(
            new ElementNotExistsCondition($this->getHTMLPage(), $this->getLocator('previewLoading'))
        );

        $this->notification->verifyAlertSuccess();

        Assert::assertEquals(self::IMAGE_ASSET_NOTIFICATION_MESSAGE, $this->notification->getMessage());
    }

    public function selectFromRepository(string $path): void
    {
        $selectFromRepoLocator = CSSLocatorBuilder::base($this->parentLocator)
            ->withDescendant($this->getLocator('selectFromRepoButton'))
            ->build();

        $this->getHTMLPage()
            ->find($selectFromRepoLocator)
            ->click();

        $this->universalDiscoveryWidget->verifyIsLoaded();
        $this->universalDiscoveryWidget->selectContent($path);
        $this->universalDiscoveryWidget->confirm();
    }

    public function specifyLocators(): array
    {
        return array_merge(
            parent::specifyLocators(),
            [
                new VisibleCSSLocator('selectFromRepoButton', '.ibexa-data-source__btn-select'),
                new VisibleCSSLocator('previewLoading', '.ibexa-field-edit--is-preview-loading'),
            ]
        );
    }

    public function getFieldTypeIdentifier(): string
    {
        return 'ezimageasset';
    }
}
