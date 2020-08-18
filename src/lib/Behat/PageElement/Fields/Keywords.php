<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields;

use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;
use PHPUnit\Framework\Assert;

class Keywords extends EzFieldElement
{
    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'Keywords';
    private $setKeywordsValueScript = <<<SCRIPT
const SELECTOR_TAGGIFY = '.ez-data-source__taggify';
const taggifyContainer = document.querySelector(SELECTOR_TAGGIFY);
const taggify = new window.Taggify({
    containerNode: taggifyContainer,
    displayLabel: false,
    displayInputValues: false,
});

const tags = [%s];
var list = tags.map(function (item) {
    return {id: item, label: item};
});

taggify.updateTags(list);
SCRIPT;

    public function __construct(UtilityContext $context, string $locator, string $label)
    {
        parent::__construct($context, $locator, $label);
        $this->fields['fieldInput'] = 'input';
    }

    public function setValue(array $parameters): void
    {
        $fieldInput = $this->context->findElement(
            sprintf('%s %s', $this->fields['fieldContainer'], $this->fields['fieldInput'])
        );
        Assert::assertNotNull($fieldInput, sprintf('Input for field %s not found.', $this->label));

        $parsedValue = implode(',', array_map(
            function (string $element) {
                return sprintf('"%s"', trim($element));
            }, explode(',', $parameters['value'])
        ));

        $this->context->getSession()->getDriver()->executeScript(sprintf($this->setKeywordsValueScript, $parsedValue));
    }

    public function getValue(): array
    {
        $fieldInput = $this->context->findElement(
            sprintf('%s %s', $this->fields['fieldContainer'], $this->fields['fieldInput'])
        );

        Assert::assertNotNull($fieldInput, sprintf('Input for field %s not found.', $this->label));

        return [$fieldInput->getValue()];
    }

    public function verifyValueInItemView(array $values): void
    {
        Assert::assertEquals(
            str_replace(', ', '', $values['value']),
            $this->context->findElement($this->fields['fieldContainer'])->getText(),
            'Field has wrong value'
        );
    }
}
