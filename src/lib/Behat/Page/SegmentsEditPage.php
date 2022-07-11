<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\AdminUi\Behat\Page;

use Behat\Mink\Session;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\Behat\Browser\Page\Page;
use Ibexa\Behat\Browser\Routing\Router;
use PHPUnit\Framework\Assert;

class SegmentsEditPage extends Page
{
    public function __construct(Session $session, Router $router)
    {
        parent::__construct($session, $router);
    }

    public function getName(): string
    {
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->setTimeout(5)->find($this->getLocator('segmentGroupInformationTitle'))->isVisible();
        Assert::assertEquals(
            'Segment group information',
            $this->getHTMLPage()->find($this->getLocator('segmentGroupInformationTitle'))->getText()
        );
    }

    protected function getRoute(): string
    {
        return '/segmentation/group/view';
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('segmentGroupInformationTitle', 'section:nth-child(1) > header > div.ez-table-header__headline'),
            new VisibleCSSLocator('segmentGroupInformationName', ' section:nth-child(1) > table > tbody > tr > td:nth-child(1)'),
            new VisibleCSSLocator('segmentGroupInformationIdentifier', ' section:nth-child(1) > table > tbody > tr > td:nth-child(2)'),
            new VisibleCSSLocator('createSegmentButton', '[data-target="#add-segment-modal"]'),
            new VisibleCSSLocator('addedSegmentName','.ez-toggle-btn-state table tbody tr:last-child td:nth-child(2)'),
            new VisibleCSSLocator('addedSegmentIdentifier','.ez-toggle-btn-state table tbody tr:last-child td:nth-child(3)'),
        ];
    }

    public function verifySegmentGroupNameInEditPage($segmentGroupName): void
    {
        $this->getHTMLPage()
            ->find($this->getLocator('segmentGroupInformationName'))
            ->assert()->textEquals($segmentGroupName);
    }

    public function verifySegmentGroupIdentifierInEditPage($segmentGroupIdentifier): void
    {
        $this->getHTMLPage()
            ->find($this->getLocator('segmentGroupInformationIdentifier'))
            ->assert()->textEquals($segmentGroupIdentifier);
    }

    public function verifySegmentNameInEditPage($addedSegmentName): void
    {
        $this->getHTMLPage()
            ->find($this->getLocator('addedSegmentName'))
            ->assert()->textEquals($addedSegmentName);
    }

    public function verifySegmentIdentifierInEditPage($addedSegmentIdentifier): void
    {
        $this->getHTMLPage()
            ->find($this->getLocator('addedSegmentIdentifier'))
            ->assert()->textEquals($addedSegmentIdentifier);
    }
}
