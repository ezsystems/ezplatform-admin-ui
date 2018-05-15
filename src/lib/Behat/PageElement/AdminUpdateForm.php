<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

use Behat\Mink\Element\NodeElement;
use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields\DefaultFieldElement;
use PHPUnit\Framework\Assert;

/** Element that describes structures in all update forms */
class AdminUpdateForm extends Element
{
    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'Admin Update Form';

    private const NEW_FIELD_TITLE_PATTERN = 'New FieldDefinition (%s)';

    private $fieldTypeMapping = [
        'Authors' => 'ezauthor',
        'Checkbox' => 'ezboolean',
        'Content relation (single)' => 'ezobjectrelation',
        'Content relations (multiple)' => 'ezobjectrelationlist',
        'Country' => 'ezcountry',
        'Date' => 'ezdate',
        'Date and time' => 'ezdatetime',
        'E-mail address' => 'ezemail',
        'File' => 'ezbinaryfile',
        'Float' => 'ezfloat',
        'ISBN' => 'ezisbn',
        'Image' => 'ezimage',
        'Integer' => 'ezinteger',
        'Keywords' => 'ezkeyword',
        'Landing Page' => 'ezlandingpage',
        'Layout' => 'ezpage',
        'Map location' => 'ezgmaplocation',
        'Media' => 'ezmedia',
        'Rating' => 'ezsrrating',
        'Rich text' => 'ezrichtext',
        'Selection' => 'ezselection',
        'Text block' => 'eztext',
        'Text line' => 'ezstring',
        'Time' => 'eztime',
        'URL' => 'ezurl',
        'User account' => 'ezuser',
    ];

    public function __construct(UtilityContext $context)
    {
        parent::__construct($context);
        $this->fields = [
            'formElement' => '.form-group',
            'mainFormSection' => 'form',
            'richTextSelector' => '.ez-data-source__richtext',
            'fieldTypesList' => '#ezrepoforms_contenttype_update_fieldTypeSelection',
            'addFieldDefinition' => 'ezrepoforms_contenttype_update_addFieldDefinition',
            'fieldDefinitionContainer' => '.ez-card--fieldtype-container:nth-child(%s)',
            'fieldDefinitionName' => '.ez-card--fieldtype-container .ez-card__header .form-check-label',
            'fieldBody' => 'ez-card__body',
            'fieldCollapsed' => 'ez-card--collapsed',
            'fieldDefinitionToggler' => '.ez-card__body-display-toggler',
            'closeButton' => '.ez-content-edit-container__close',
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
    public function fillFieldWithValue(string $fieldName, $value, ?string $containerName = null): void
    {
        $newContainerName = (!$containerName) ? $containerName : sprintf($this::NEW_FIELD_TITLE_PATTERN, $this->fieldTypeMapping[$containerName]);
        $fieldElement = $this->getField($fieldName, $newContainerName);
        $fieldElement->setValue($value);
    }

    /**
     * Verify that field values are set.
     *
     * @param string $fieldName
     * @param string $value
     * @param null|string $containerName for fields that defines new field type in content type
     *
     * @throws \Exception
     */
    public function verifyFieldHasValue(string $fieldName, $value, ?string $containerName = null): void
    {
        $fieldElement = $this->getField($fieldName, $containerName);
        Assert::assertEquals(
            $value,
            $fieldElement->getValue(),
            'Field has wrong value'
        );
    }

    public function getField(string $fieldName, ?string $containerName = null): DefaultFieldElement
    {
        if ($containerName !== null) {
            $container = $this->getFieldDefinitionContainerLocator($containerName);
        } else {
            $container = $this->fields['mainFormSection'];
        }

        return ElementFactory::createElement($this->context, DefaultFieldElement::ELEMENT_NAME, $fieldName, $container);
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
        $form = $this->context->getElementByText(sprintf($this::NEW_FIELD_TITLE_PATTERN, $this->fieldTypeMapping[$fieldName]), $this->fields['fieldDefinitionName']);
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

    /**
     * Expand field definition if it is collapsed.
     *
     * @param string $fieldName
     */
    public function expandFieldDefinition(string $fieldName): void
    {
        $container = $this->context->findElement($this->getFieldDefinitionContainerLocator(sprintf($this::NEW_FIELD_TITLE_PATTERN, $this->fieldTypeMapping[$fieldName])));
        Assert::assertNotNull($container, sprintf('Definition for field %s not found', $fieldName));

        if (strpos($container->getAttribute('class'), $this->fields['fieldCollapsed']) !== false) {
            $container->find('css', $this->fields['fieldDefinitionToggler'])->click();
        }
    }

    /**
     * Returns NodeElement that contains all fields for specified content type field type.
     *
     * @param string $containerName
     *
     * @return NodeElement
     */
    public function getFieldDefinitionContainerLocator(string $containerName): string
    {
        $containerIndex = $this->context->getElementPositionByText($containerName, $this->fields['fieldDefinitionName']);

        return sprintf($this->fields['fieldDefinitionContainer'], $containerIndex);
    }
}
