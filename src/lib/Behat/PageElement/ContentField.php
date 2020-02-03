<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields\EzFieldElement;

class ContentField extends Element
{
    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'ContentField';

    public const FIELD_TYPE_CLASS_REGEX = '/ez[a-z]*-field/';

    public function __construct(UtilityContext $context)
    {
        parent::__construct($context);
        $this->fields = [
            'nthFieldContainer' => '.ez-content-field:nth-child(%s)',
            'fieldName' => '.ez-content-field-name',
            'fieldValue' => '.ez-content-field-value',
            'fieldValueContainer' => ':first-child',
        ];
    }

    public function verifyFieldHasValue(string $label, array $value): void
    {
        $fieldIndex = $this->context->getElementPositionByText(sprintf('%s:', $label), $this->fields['fieldName']);
        $fieldLocator = sprintf(
            '%s %s',
            sprintf($this->fields['nthFieldContainer'], $fieldIndex + 1),
            $this->fields['fieldValue']
        );

        $fieldClass = $this->context->findElement(sprintf('%s %s', $fieldLocator, $this->fields['fieldValueContainer']))->getAttribute('class');
        $fieldType = $this->getFieldType($fieldClass);

        $fieldElement = ElementFactory::createElement($this->context, EzFieldElement::getFieldNameByInternalName($fieldType), $fieldLocator, $label);
        $fieldElement->verifyValueInItemView($value);
    }

    private function getFieldType(string $fieldClass): string
    {
        if (strpos($fieldClass, 'ez-table') !== false) {
            return 'ezmatrix';
        }

        if (!$fieldClass) {
            return 'ezboolean';
        }

        preg_match($this::FIELD_TYPE_CLASS_REGEX, $fieldClass, $matches);

        return explode('-', $matches[0])[0];
    }
}
