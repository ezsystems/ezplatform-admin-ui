<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\Component\Fields;

use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

class TextBlock extends FieldTypeComponent
{
    public function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('fieldInput', 'textarea'),
        ];
    }

    public function getFieldTypeIdentifier(): string
    {
        return 'eztext';
    }
}
