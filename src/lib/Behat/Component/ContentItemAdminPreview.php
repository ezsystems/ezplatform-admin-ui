<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\Component;

use Behat\Mink\Session;
use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Locator\CSSLocator;
use Ibexa\Behat\Browser\Locator\CSSLocatorBuilder;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Traversable;

class ContentItemAdminPreview extends Component
{
    /** @var \Ibexa\AdminUi\Behat\Component\Fields\FieldTypeComponentInterface[] */
    private $fieldTypeComponents;

    public function __construct(Session $session, Traversable $fieldTypeComponents)
    {
        parent::__construct($session);
        $this->fieldTypeComponents = iterator_to_array($fieldTypeComponents);
    }

    public function verifyFieldHasValues(string $fieldLabel, array $expectedValues, ?string $fieldTypeIdentifier)
    {
        $fieldPosition = $this->getFieldPosition($fieldLabel);
        $nthFieldLocator = new VisibleCSSLocator('', sprintf($this->getLocator('nthFieldContainer')->getSelector(), $fieldPosition));

        $fieldValueLocator = CSSLocatorBuilder::base($nthFieldLocator)->withDescendant($this->getLocator('fieldValue'))->build();
        $fieldTypeIdentifier = $fieldTypeIdentifier ?? $this->detectFieldTypeIdentifier($fieldValueLocator);

        foreach ($this->fieldTypeComponents as $fieldTypeComponent) {
            if ($fieldTypeComponent->getFieldTypeIdentifier() === $fieldTypeIdentifier) {
                $fieldTypeComponent->setParentLocator($fieldValueLocator);
                $fieldTypeComponent->verifyValueInItemView($expectedValues);

                return;
            }
        }
    }

    public function verifyIsLoaded(): void
    {
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('nthFieldContainer', 'div.ez-content-field:nth-of-type(%s)'),
            new VisibleCSSLocator('fieldName', '.ez-content-field-name'),
            new VisibleCSSLocator('fieldValue', '.ez-content-field-value'),
            new VisibleCSSLocator('fieldValueContainer', ':first-child'),
        ];
    }

    private function getFieldPosition(string $fieldLabel): int
    {
        $searchText = sprintf('%s:', $fieldLabel);

        $fields = $this->getHTMLPage()->findAll($this->getLocator('fieldName'));

        $fieldPosition = 1;
        foreach ($fields as $field) {
            if ($field->getText() === $searchText) {
                return $fieldPosition;
            }

            ++$fieldPosition;
        }
    }

    private function detectFieldTypeIdentifier(CSSLocator $fieldValueLocator)
    {
        $fieldClass = $this->getHTMLPage()
            ->find(CSSLocatorBuilder::base($fieldValueLocator)->withDescendant($this->getLocator('fieldValueContainer'))->build())
            ->getAttribute('class')
        ;

        if ('ez-scrollable-table-wrapper mb-0' === $fieldClass) {
            return 'ezuser';
        }

        if (false !== strpos($fieldClass, 'ez-scrollable-table-wrapper')) {
            return 'ezmatrix';
        }

        if ('' === $fieldClass) {
            return 'ezboolean';
        }

        $fieldTypeIdentifierRegex = '/ez[a-z]*-field/';
        preg_match($fieldTypeIdentifierRegex, $fieldClass, $matches);

        return explode('-', $matches[0])[0];
    }
}
