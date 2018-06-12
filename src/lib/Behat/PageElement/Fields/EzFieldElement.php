<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields;

use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Element;
use PHPUnit\Framework\Assert;

abstract class EzFieldElement extends Element
{
    protected $label;

    private static $FIELD_TYPE_MAPPING = [
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

    public static function getFieldNameByInternalName(string $internalFieldName): string
    {
        return static::$FIELD_TYPE_MAPPING[$internalFieldName];
    }

    public static function getFieldInternalNameByName(string $fieldName): string
    {
        return array_search($fieldName, static::$FIELD_TYPE_MAPPING);
    }

    public function __construct(UtilityContext $context, string $locator, string $label)
    {
        parent::__construct($context);
        $this->label = $label;
        $this->fields = [
            'fieldContainer' => $locator,
            'fieldLabel' => '.ez-field-edit__label-wrapper',
            'fieldData' => '.ez-field-edit__data',
        ];
    }

    abstract public function setValue(array $parameters): void;

    abstract public function getValue(): array;

    public function verifyValue(array $value): void
    {
        Assert::assertEquals(
            $value['value'],
            $this->getValue()[0],
            sprintf('Field %s has wrong value', $value['label'])
        );
    }
}
