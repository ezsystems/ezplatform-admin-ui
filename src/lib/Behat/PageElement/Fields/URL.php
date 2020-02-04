<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields;

use EzSystems\Behat\Browser\Context\BrowserContext;
use PHPUnit\Framework\Assert;

class URL extends EzFieldElement
{
    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'URL';

    public function __construct(BrowserContext $context, string $locator, string $label)
    {
        parent::__construct($context, $locator, $label);
        $this->fields['url'] = '#ezplatform_content_forms_content_edit_fieldsData_ezurl_value_link';
        $this->fields['text'] = '#ezplatform_content_forms_content_edit_fieldsData_ezurl_value_text';
    }

    public function setValue(array $parameters): void
    {
        $this->setSpecificFieldValue('url', $parameters['url']);
        $this->setSpecificFieldValue('text', $parameters['text']);
    }

    public function setSpecificFieldValue(string $coordinateName, string $value): void
    {
        $fieldInput = $this->context->findElement(
            sprintf('%s %s', $this->fields['fieldContainer'], $this->fields[$coordinateName])
        );

        Assert::assertNotNull($fieldInput, sprintf('Input %s for field %s not found.', $coordinateName, $this->label));

        $fieldInput->setValue('');
        $fieldInput->setValue($value);
    }

    public function getValue(): array
    {
        return [
            'url' => $this->getSpecificFieldValue('url'),
            'text' => $this->getSpecificFieldValue('text'),
            ];
    }

    public function getSpecificFieldValue(string $coordinateName): string
    {
        $fieldInput = $this->context->findElement(
            sprintf('%s %s', $this->fields['fieldContainer'], $this->fields[$coordinateName])
        );

        Assert::assertNotNull($fieldInput, sprintf('Input %s for field %s not found.', $coordinateName, $this->label));

        return $fieldInput->getValue();
    }

    public function verifyValue(array $value): void
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
            $this->context->findElement($this->fields['fieldContainer'])->getText(),
            'Field has wrong value'
        );
        Assert::assertEquals(
            $values['url'],
            $this->context->findElement(sprintf('%s %s', $this->fields['fieldContainer'], 'a'))->getAttribute('href'),
            'Field has wrong value'
        );
    }
}
