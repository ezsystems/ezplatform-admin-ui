<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

use EzSystems\Behat\API\ContentData\FieldTypeNameConverter;
use EzSystems\Behat\Browser\Context\BrowserContext;
use EzSystems\Behat\Browser\Factory\ElementFactory;
use EzSystems\Behat\Browser\Element\Element;

class ContentField extends Element
{
    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'ContentField';

    public const FIELD_TYPE_CLASS_REGEX = '/ez[a-z]*-field/';

    public function __construct(BrowserContext $context)
    {
        parent::__construct($context);
        $this->fields = [
            'nthFieldContainer' => 'div.ez-content-field:nth-of-type(%s)',
            'fieldName' => '.ez-content-field-name',
            'fieldValue' => '.ez-content-field-value',
            'fieldValueContainer' => ':first-child',
        ];
    }

    public function verifyFieldHasValue(string $label, array $fieldData): void
    {
        $fieldIndex = $this->context->getElementPositionByText(sprintf('%s:', $label), $this->fields['fieldName']);
        $fieldLocator = sprintf(
            '%s %s',
            sprintf($this->fields['nthFieldContainer'], $fieldIndex),
            $this->fields['fieldValue']
        );

        if (array_key_exists('fieldType', $fieldData)) {
            $fieldType = $fieldData['fieldType'];
        } else {
            $fieldClass = $this->context->findElement(sprintf('%s %s', $fieldLocator, $this->fields['fieldValueContainer']))->getAttribute('class');
            $fieldTypeIdentifier = $this->getFieldTypeIdentifier($fieldClass);
            $fieldType = FieldTypeNameConverter::getFieldTypeNameByIdentifier($fieldTypeIdentifier);
        }

        $fieldElement = ElementFactory::createElement($this->context, $fieldType, $fieldLocator, $label);
        $fieldElement->verifyValueInItemView($fieldData);
    }

    private function getFieldTypeIdentifier(string $fieldClass): string
    {
        if (strpos($fieldClass, 'ez-table') !== false) {
            return 'ezmatrix';
        }

        if ($fieldClass === '') {
            return 'ezboolean';
        }

        preg_match($this::FIELD_TYPE_CLASS_REGEX, $fieldClass, $matches);
        $matchedValue = explode('-', $matches[0])[0];

        return $matchedValue;
    }
}
