<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\Component\Fields;

use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Locator\CSSLocatorBuilder;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use PHPUnit\Framework\Assert;

abstract class FieldTypeComponent extends Component implements FieldTypeComponentInterface
{
    /** @var \Ibexa\Behat\Browser\Locator\VisibleCSSLocator */
    protected $parentLocator;

    public function setValue(array $parameters): void
    {
        $fieldSelector = CSSLocatorBuilder::base($this->parentLocator)
            ->withDescendant($this->getLocator('fieldInput'))
            ->build();

        $value = $parameters['value'];
        $this->getHTMLPage()->find($fieldSelector)->setValue($value);
    }

    public function getValue(): array
    {
        $fieldSelector = CSSLocatorBuilder::base($this->parentLocator)
            ->withDescendant($this->getLocator('fieldInput'))
            ->build();

        $value = $this->getHTMLPage()->find($fieldSelector)->getValue();

        return [$value];
    }

    public function verifyValueInItemView(array $values): void
    {
        Assert::assertEquals(
            $values['value'],
            $this->getHTMLPage()->find($this->parentLocator)->getText(),
            'Field has wrong value'
        );
    }

    public function setParentLocator(VisibleCSSLocator $locator): void
    {
        $this->parentLocator = $locator;
    }

    abstract public function getFieldTypeIdentifier(): string;

    public function verifyValueInEditView(array $value): void
    {
        Assert::assertEquals(
            $value['value'],
            $this->getValue()[0]
        );
    }

    public function verifyIsLoaded(): void
    {
    }
}
