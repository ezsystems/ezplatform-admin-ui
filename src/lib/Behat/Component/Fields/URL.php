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

class URL extends FieldTypeComponent
{
    public function setValue(array $parameters): void
    {
        $this->setSpecificFieldValue('url', $parameters['url']);
        $this->setSpecificFieldValue('text', $parameters['text']);
    }

    public function setSpecificFieldValue(string $fieldName, string $value): void
    {
        $fieldSelector = CSSLocatorBuilder::base($this->parentLocator)
            ->withDescendant($this->getLocator($fieldName))
            ->build();

        $this->getHTMLPage()->find($fieldSelector)->setValue($value);
    }

    public function getValue(): array
    {
        return [
            'url' => $this->getSpecificFieldValue('url'),
            'text' => $this->getSpecificFieldValue('text'),
            ];
    }

    public function getSpecificFieldValue(string $fieldName): string
    {
        $fieldSelector = CSSLocatorBuilder::base($this->parentLocator)
            ->withDescendant($this->getLocator($fieldName))
            ->build();

        return $this->getHTMLPage()->find($fieldSelector)->getValue();
    }

    public function verifyValueInEditView(array $value): void
    {
        Assert::assertEquals(
            $value['url'],
            $this->getValue()['url'],
            sprintf('Field %s has wrong value', $value['label'])
        );
        Assert::assertEquals(
            $value['text'],
            $this->getValue()['text'],
            sprintf('Field %s has wrong value', $value['label'])
        );
    }

    public function verifyValueInItemView(array $values): void
    {
        Assert::assertEquals(
            $values['text'],
            $this->getHTMLPage()->find($this->parentLocator)->getText(),
            'Field has wrong value'
        );

        $urlSelector = CSSLocatorBuilder::base($this->parentLocator)
            ->withDescendant(new VisibleCSSLocator('', 'a'))
            ->build();

        Assert::assertEquals(
            $values['url'],
            $this->getHTMLPage()->find($urlSelector)->getAttribute('href'),
            'Field has wrong value'
        );
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('url', '#ezplatform_content_forms_content_edit_fieldsData_ezurl_value_link'),
            new VisibleCSSLocator('text', '#ezplatform_content_forms_content_edit_fieldsData_ezurl_value_text'),
        ];
    }

    public function getFieldTypeIdentifier(): string
    {
        return 'ezurl';
    }
}
