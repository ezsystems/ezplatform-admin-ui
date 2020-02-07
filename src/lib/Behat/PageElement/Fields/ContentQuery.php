<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields;

use Behat\Mink\Element\NodeElement;
use EzSystems\Behat\Browser\Context\BrowserContext;
use PHPUnit\Framework\Assert;

class ContentQuery extends NonEditableField
{
    public const ELEMENT_NAME = 'Content query';

    public function __construct(BrowserContext $context, string $locator, string $label)
    {
        parent::__construct($context, $locator, $label);
        $this->fields['queryResultItem'] = 'p a';
    }

    public function verifyValueInItemView(array $values): void
    {
        $expecteditems = explode(',', $values['value']);
        $actualItems = $this->getValueInItemView();
        $commonItems = array_intersect($expecteditems, $actualItems);

        Assert::assertEquals([], array_diff($expecteditems, $commonItems));
    }

    private function getValueInItemView(): array
    {
        $items = $this->context->findAllElements(sprintf('%s %s', $this->fields['fieldContainer'], $this->fields['queryResultItem']));

        return array_map(function (NodeElement $element) {
            return $element->getText();
        }, $items);
    }
}
