<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields;

use EzSystems\Behat\Browser\Context\BrowserContext;
use EzSystems\Behat\Browser\Element\Element;
use PHPUnit\Framework\Assert;

abstract class EzFieldElement extends Element
{
    protected $label;

    public function __construct(BrowserContext $context, string $locator, string $label)
    {
        parent::__construct($context);
        $this->label = $label;
        $this->fields = [
            'fieldContainer' => $locator,
            'fieldLabel' => '.ez-field-edit__label-wrapper',
            'fieldData' => '.ez-field-edit__data',
        ];
    }

    abstract public function setValue(array $parameters): void;

    abstract public function getValue(): array;

    abstract public function verifyValueInItemView(array $values): void;

    public function verifyValue(array $value): void
    {
        Assert::assertEquals(
            $value['value'],
            $this->getValue()[0],
            sprintf('Field %s has wrong value', $value['label'])
        );
    }
}
