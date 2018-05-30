<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields\EzFieldElement;
use PHPUnit\Framework\Assert;

class ContentUpdateForm extends Element
{
    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'Content Update Form';

    public const FIELD_TYPE_CLASS_REGEX = '/ez-field-edit--ez[a-z]*/';

    private $fieldTypeMapping = [
        'ezauthor' => 'Authors',
        'ezboolean' => 'Checkbox',
        'ezobjectrelation' => 'Content relation (single)',
        'ezobjectrelationlist' => 'Content relations (multiple)',
        'ezcountry' => 'Country',
        'ezdate' => 'Date',
        'ezdatetime' => 'Date and time',
        'ezemail' => 'E-mail address',
        'ezbinaryfile' => 'File',
        'ezfloat' => 'Float',
        'ezisbn' => 'ISBN',
        'ezimage' => 'Image',
        'ezinteger' => 'Integer',
        'ezkeyword' => 'Keywords',
        'ezlandingpage' => 'Landing Page',
        'ezpage' => 'Layout',
        'ezgmaplocation' => 'Map location',
        'ezmedia' => 'Media',
        'ezsrrating' => 'Rating',
        'ezrichtext' => 'Rich text',
        'ezselection' => 'Selection',
        'eztext' => 'Text block',
        'ezstring' => 'Text line',
        'eztime' => 'Time',
        'ezurl' => 'URL',
        'ezuser' => 'User account',
    ];

    public function __construct(UtilityContext $context)
    {
        parent::__construct($context);
        $this->fields = [
            'formElement' => '[name=ezrepoforms_content_edit]',
            'closeButton' => '.ez-content-edit-container__close',
            'fieldLabel' => '.ez-field-edit__label-wrapper label.ez-field-edit__label, .ez-field-edit__label-wrapper legend',
            'nthField' => '.ez-field-edit:nth-child(%s)',
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
     * @param null|string $containerLocator for update form
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

        $fieldClass = $this->context->findElement(sprintf($this->fields['nthField'], $fieldPosition))->getAttribute('class');

        preg_match($this::FIELD_TYPE_CLASS_REGEX, $fieldClass, $matches);

        $fieldType = explode('--', $matches[0])[1];
        $fieldLocator = sprintf($this->fields['nthField'], $fieldPosition);

        return ElementFactory::createElement($this->context, $this->fieldTypeMapping[$fieldType], $fieldLocator, $fieldName);
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
    public function verifyFieldHasValue(array $fieldData): void
    {
        $this->getField($fieldData['label'])->verifyValue($fieldData);
    }

    public function closeUpdateForm(): void
    {
        $this->context->findElement($this->fields['closeButton'])->click();
    }
}
