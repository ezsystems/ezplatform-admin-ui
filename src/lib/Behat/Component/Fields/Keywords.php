<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\Component\Fields;

use Ibexa\Behat\Browser\Element\ElementInterface;
use Ibexa\Behat\Browser\Locator\CSSLocatorBuilder;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use PHPUnit\Framework\Assert;

class Keywords extends FieldTypeComponent
{
    private $setKeywordsValueScript = <<<SCRIPT
const SELECTOR_TAGGIFY = '.ibexa-data-source__taggify';
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

    public function setValue(array $parameters): void
    {
        $parsedValue = implode(',', array_map(
            static function (string $element) {
                return sprintf('"%s"', trim($element));
            }, explode(',', $parameters['value'])
        ));

        $this->getSession()->getDriver()->executeScript(sprintf($this->setKeywordsValueScript, $parsedValue));
    }

    public function verifyValueInItemView(array $values): void
    {
        $expectedValues = $this->parseValueString($values['value']);

        $keywordItemLocator = CSSLocatorBuilder::base($this->parentLocator)
            ->withDescendant($this->getLocator('keywordItem'))
            ->build();

        $actualValues = $this->getHTMLPage()
            ->findAll($keywordItemLocator)
            ->map(static function (ElementInterface $element) {
                return $element->getText();
            });
        sort($actualValues);

        Assert::assertEquals($expectedValues, $actualValues);
    }

    private function parseValueString(string $value): array
    {
        $parsedValues = [];

        foreach (explode(',', $value) as $singleValue) {
            $parsedValues[] = trim($singleValue);
        }

        sort($parsedValues);

        return $parsedValues;
    }

    public function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('fieldInput', 'input'),
            new VisibleCSSLocator('keywordItem', '.ez-keyword__item'),
        ];
    }

    public function getFieldTypeIdentifier(): string
    {
        return 'ezkeyword';
    }
}
