<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

use Behat\Mink\Element\NodeElement;
use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;

/** Element that describes structures in all update forms */
class UpdateForm extends Element
{
    private $fieldTypesMapping = [
        'Text line' => 'ezstring',
        'Country' => 'ezcountry',
        'Date' => 'ezdate',
    ];

    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'Admin Update Form';

    public function __construct(UtilityContext $context)
    {
        parent::__construct($context);
        $this->fields = [
            'formElement' => '.form-group',
            'mainFormSection' => 'form',
            'richTextSelector' => '.ez-data-source__richtext',
            'fieldTypesList' => '#ezrepoforms_contenttype_update_fieldTypeSelection',
            'addFieldDefinition' => 'ezrepoforms_contenttype_update_addFieldDefinition',
            'fieldDefinitionContainer' => '.ez-card--fieldtype-container',
            'fieldDefinitionName' => '.ez-card--fieldtype-container .ez-card__header .form-check-label',
            'button' => 'button',
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
     * @param string $value
     * @param null|string $containerName for fields that defines new field type in content type
     *
     * @throws \Exception
     */
    public function fillFieldWithValue(string $fieldName, string $value, ?string $containerName = null): void
    {
        if ($containerName !== null) {
            $container = $this->getFieldDefinitionContainer($containerName);
        } else {
            $container = $this->context->getSession()->getPage();
        }

        $fieldNode = $container->findField($fieldName);

        if ($fieldNode === null) {
            throw new \Exception(sprintf('Field %s not found.', $fieldName));
        }

        switch ($fieldNode->getAttribute('type')) {
            case 'text':
            case 'email':
                $fieldNode->setValue('');
                $fieldNode->setValue($value);
                break;
            case 'checkbox':
                $fieldNode->setValue(filter_var($value, FILTER_VALIDATE_BOOLEAN));
                break;
            case 'radio':
                if ($fieldNode->isChecked() !== ($value === 'true')) {
                    $fieldNode->click();
                }
                break;
            default:
                throw new \Exception(sprintf('Field type "%s" not defined in UpdateForm.', $fieldNode->getAttribute('type')));
        }
    }

    public function fillRichtextWithValue(string $value): void
    {
        $summaryField = $this->context->findElement($this->fields['richTextSelector']);
        $summaryField->click();
        $summaryField->setValue($value);
    }

    /**
     * Returns NodeElement that contains all fields for specified content type field type.
     *
     * @param string $containerName
     *
     * @return NodeElement
     */
    public function getFieldDefinitionContainer(string $containerName): NodeElement
    {
        $containerIndex = $this->context->getElementPositionByText($containerName, $this->fields['fieldDefinitionName']);

        return $this->context->findAllWithWait($this->fields['fieldDefinitionContainer'])[$containerIndex - 1];
    }

    /**
     * Select field definition with given name from select list.
     *
     * @param string $fieldName
     */
    public function selectFieldDefinition(string $fieldName): void
    {
        $this->context->findElement($this->fields['fieldTypesList'], $this->defaultTimeout)->selectOption($fieldName);
    }

    public function clickAddFieldDefinition(): void
    {
        $this->context->pressButton($this->fields['addFieldDefinition']);
    }

    /**
     * Verifies that form container with new field definition of given name is visible.
     *
     * @param string $fieldName
     *
     * @throws \Exception
     */
    public function verifyNewFieldDefinitionFormExists(string $fieldName): void
    {
        $form = $this->context->getElementByText(
            sprintf('New FieldDefinition (%s)', $this->fieldTypesMapping[$fieldName]),
            $this->fields['fieldDefinitionName']
        );
        if ($form === null) {
            throw new \Exception('Field definition not added to the form.');
        }
    }

    /**
     * Click button with given label.
     *
     * @param string $label
     * @param int $indexOfButton
     */
    public function clickButton(string $label, int $indexOfButton = 0): void
    {
        $formButtons = $this->context->findAllWithWait($this->fields['button'], $this->context->findElement($this->fields['mainFormSection']));
        $filteredButtons = array_filter($formButtons, function ($element) use ($label) { return $element->getText() === $label; });

        $filteredButtons[$indexOfButton]->click();
    }
}
