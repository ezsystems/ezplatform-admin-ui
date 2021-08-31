<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\Component\Fields;

use Ibexa\Behat\Browser\Locator\CSSLocatorBuilder;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use PHPUnit\Framework\Assert;

class Authors extends FieldTypeComponent
{
    public function setValue(array $parameters): void
    {
        $name = $parameters['name'];
        $email = $parameters['email'];

        $nameSelector = CSSLocatorBuilder::base($this->parentLocator)->withDescendant($this->getLocator('nameFieldInput'))->build();
        $emailSelector = CSSLocatorBuilder::base($this->parentLocator)->withDescendant($this->getLocator('emailFieldInput'))->build();

        $this->getHTMLPage()->find($nameSelector)->setValue($name);
        $this->getHTMLPage()->find($emailSelector)->setValue($email);
    }

    public function getValue(): array
    {
        $nameSelector = CSSLocatorBuilder::base($this->parentLocator)->withDescendant($this->getLocator('nameFieldInput'))->build();
        $emailSelector = CSSLocatorBuilder::base($this->parentLocator)->withDescendant($this->getLocator('emailFieldInput'))->build();

        return [
            'name' => $this->getHTMLPage()->find($nameSelector)->getValue(),
            'email' => $this->getHTMLPage()->find($emailSelector)->getValue(),
        ];
    }

    public function verifyValueInEditView(array $value): void
    {
        $expectedName = $value['name'];
        $expectedEmail = $value['email'];

        $actualFieldValues = $this->getValue();
        Assert::assertEquals(
            $expectedName,
            $actualFieldValues['name'],
            sprintf('Field %s has wrong value', $value['label'])
        );

        Assert::assertEquals(
            $expectedEmail,
            $actualFieldValues['email'],
            sprintf('Field %s has wrong value', $value['label'])
        );
    }

    public function verifyValueInItemView(array $values): void
    {
        Assert::assertEquals(
            sprintf('%s <%s>', $values['name'], $values['email']),
            $this->getHTMLPage()->find($this->parentLocator)->getText(),
            'Field has wrong value'
        );
    }

    public function getFieldTypeIdentifier(): string
    {
        return 'ezauthor';
    }

    public function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('nameFieldInput', '.ez-data-source__field--name input'),
            new VisibleCSSLocator('emailFieldInput', '.ez-data-source__field--email input'),
            new VisibleCSSLocator('fieldValueInContentItemView', '.ez-content-field-value'),
        ];
    }
}
