<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

use Behat\Mink\Element\NodeElement;

/** Element that describes structures in all update forms */
class UpdateForm extends Element
{
    protected $fields = [
        'formElement' => '.form-group',
        'mainFormSection' => '.px-5:nth-child(1) .card-body',
        'fieldTypesList' => '#ezrepoforms_contenttype_update_fieldTypeSelection',
        'addFieldDefinition' => 'ezrepoforms_contenttype_update_addFieldDefinition',
        'fieldDefinitionContainer' => '.ez-card--fieldtype-container',
        'fieldDefinitionName' => '.ez-card--fieldtype-container .ez-card__header .form-check-label',
    ];

    private $fieldTypesMapping = [
        'Text line' => 'ezstring',
        'Country' => 'ezcountry',
        'Date' => 'ezdate',
    ];

    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'Admin Update Form';
    public const MAIN_FORM_SECTION = 'mainFormSection';

    public function verifyVisibility(): void
    {
        $this->context->waitUntilElementIsVisible($this->fields['formElement']);
    }

    public function fillFieldWithValue(string $fieldName, string $value, ?string $containerName = null): void
    {
        if ($containerName !== null) {
            $container = $this->getContainerByHeader($containerName);
        } else {
            $container = $this->context->getSession()->getPage();
        }

        $fieldNode = $container->findField($fieldName);

        if ($fieldNode === null) {
            throw new \Exception(sprintf('Field %s not found.', $fieldName));
        }

        $fieldNode->setValue('');
        $fieldNode->setValue($value);
    }

    public function getContainerByHeader(string $containerName): NodeElement
    {
        $containerIndex = $this->context->getElementPositionByText($containerName, $this->fields['fieldDefinitionName']);

        return $this->context->findAllWithWait($this->fields['fieldDefinitionContainer'])[$containerIndex - 1];
    }

    public function selectFieldDefinition(string $fieldName)
    {
        $this->context->findElement($this->fields['fieldTypesList'], $this->defaultTimeout)->selectOption($fieldName);
    }

    public function clickAddFieldDefinition()
    {
        $this->context->pressButton($this->fields['addFieldDefinition']);
    }

    public function verifyNewFieldDefinitionFormExists(string $fieldName)
    {
        $form = $this->context->getElementByText(
            sprintf('New FieldDefinition (%s)', $this->fieldTypesMapping[$fieldName]),
            $this->fields['fieldDefinitionName']
        );
        if ($form === null) {
            throw new \Exception('Field definition not added to the form.');
        }
    }
}
