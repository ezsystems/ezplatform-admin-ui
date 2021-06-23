<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\Component\Fields;

use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\Behat\Browser\Locator\CSSLocatorBuilder;

class TextLine extends FieldTypeComponent
{
    public function setValue(array $parameters): void
    {
        $fieldSelector = CSSLocatorBuilder::base($this->parentLocator)
            ->withDescendant($this->getLocator('fieldInput'))
            ->build();

        $value = array_values($parameters)[0];
        $this->getHTMLPage()->find($fieldSelector)->setValue($value);
    }

    public function specifyLocators(): array
    {
        return [
                new VisibleCSSLocator('fieldInput', 'input'),
            ];
    }

    public function getFieldTypeIdentifier(): string
    {
        return 'ezstring';
    }
}
