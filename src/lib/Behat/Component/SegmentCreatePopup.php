<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\Component;

use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use PHPUnit\Framework\Assert;

class SegmentCreatePopup extends Component
{
    public function verifyIsLoaded(): void
    {
        Assert::assertTrue($this->getHTMLPage()->find($this->getLocator('creatingANewSegmentWindow'))->isVisible());
    }

    protected function specifyLocators(): array
    {
        return
        [
            new VisibleCSSLocator('creatingANewSegmentWindow', '#add-segment-modal > div > div'),
            new VisibleCSSLocator('identifierTextbox', '#segment_create_identifier'),
            new VisibleCSSLocator('nameTextbox', '#segment_create_name'),
            new VisibleCSSLocator('createButton', '#segment_create_create'),
        ];
    }

    public function fillSegmentFieldWithValue(string $name, $identifier): void
    {
        $this->verifyIsLoaded();
        $this->getHTMLPage()->find($this->getLocator('identifierTextbox'))->setValue($identifier);
        $this->getHTMLPage()->find($this->getLocator('nameTextbox'))->setValue($name);
    }

    public function confirmNewSegmentAddition(): void
    {
        $this->getHTMLPage()->find($this->getLocator('createButton'))->click();
    }
}
