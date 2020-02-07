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
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields\EzFieldElement;
use PHPUnit\Framework\Assert;

class ContentUpdateForm extends Element
{
    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'Content Update Form';

    public const FIELD_TYPE_CLASS_REGEX = '/ez-field-edit--ez[a-z]*/';

    public function __construct(BrowserContext $context)
    {
        parent::__construct($context);
        $this->fields = [
            'formElement' => '[name=ezplatform_content_forms_content_edit]',
            'closeButton' => '.ez-content-edit-container__close',
            'fieldLabel' => '.ez-field-edit__label-wrapper label.ez-field-edit__label, .ez-field-edit__label-wrapper legend, .ez-card > .card-body > div > legend',
            'nthField' => '.ez-card .card-body > div:nth-of-type(%s)',
            'editableFieldClass' => 'ez-field-edit',
            'fieldOfType' => '.ez-field-edit--%s',
        ];
    }

    public function verifyVisibility(): void
    {
        $this->context->waitUntilElementIsVisible($this->fields['formElement']);
    }

    /**
     * Fill in field values depending on the field type.
     *
     * @param string $fieldName
     * @param array $value
     * @param string|null $containerLocator for update form
     *
     * @throws \Exception
     */
    public function fillFieldWithValue(string $fieldName, array $value): void
    {
        $fieldElement = $this->getField($fieldName);
        $fieldElement->setValue($value);
    }

    public function getField(string $fieldName): EzFieldElement
    {
        $fieldPosition = $this->context->getElementPositionByText($fieldName, $this->fields['formElement'] . ' ' . $this->fields['fieldLabel']);
        if ($fieldPosition === 0) {
            Assert::fail(sprintf('Field %s not found.', $fieldName));
        }

        $fieldLocator = sprintf($this->fields['nthField'], $fieldPosition);

        $isEditable = $this->context->findElement($fieldLocator)->hasClass($this->fields['editableFieldClass']);
        if (!$isEditable) {
            $fieldType = strtolower($fieldName);
        } else {
            $fieldClass = $this->context->findElement($fieldLocator)->getAttribute('class');
            preg_match($this::FIELD_TYPE_CLASS_REGEX, $fieldClass, $matches);
            $fieldType = explode('--', $matches[0])[1];
        }

        return ElementFactory::createElement($this->context, FieldTypeNameConverter::getFieldTypeNameByIdentifier($fieldType), $fieldLocator, $fieldName);
    }

    /**
     * Verify that field values are set.
     *
     * @param string $fieldName
     * @param string $value
     * @param string|null $containerName for fields that defines new field type in content type
     *
     * @throws \Exception
     */
    public function verifyFieldHasValue(array $fieldData): void
    {
        $this->getField($fieldData['label'])->verifyValue($fieldData);
    }

    public function closeUpdateForm(): void
    {
        $this->context->findElement($this->fields['closeButton'])->click();
    }
}
