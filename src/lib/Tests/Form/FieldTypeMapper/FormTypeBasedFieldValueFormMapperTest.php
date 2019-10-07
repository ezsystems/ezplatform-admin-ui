<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Tests\Form\FieldTypeMapper;

use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use EzSystems\EzPlatformAdminUi\Form\FieldTypeMapper\FormTypeBasedFieldValueFormMapper;

class FormTypeBasedFieldValueFormMapperTest extends BaseMapperTest
{
    public function testMapFieldValueFormNoLanguageCode()
    {
        $mapper = new FormTypeBasedFieldValueFormMapper($this->fieldTypeService);

        $fieldDefinition = new FieldDefinition([
            'names' => [],
            'isRequired' => false,
            'fieldSettings' => ['isMultiple' => false, 'options' => []],
        ]);

        $this->data->expects($this->once())
            ->method('__get')
            ->with('fieldDefinition')
            ->willReturn($fieldDefinition);

        $this->config
            ->method('getOption')
            ->willReturnMap([
                ['languageCode', null, 'eng-GB'],
                ['mainLanguageCode', null, 'eng-GB'],
            ]);

        $mapper->mapFieldValueForm($this->fieldForm, $this->data);
    }

    public function testMapFieldValueFormWithLanguageCode()
    {
        $mapper = new FormTypeBasedFieldValueFormMapper($this->fieldTypeService);

        $fieldDefinition = new FieldDefinition([
            'names' => ['eng-GB' => 'foo'],
            'isRequired' => false,
            'fieldSettings' => ['isMultiple' => false, 'options' => []],
        ]);
        $this->data->expects($this->once())
            ->method('__get')
            ->with('fieldDefinition')
            ->willReturn($fieldDefinition);

        $this->config
            ->method('getOption')
            ->with('languageCode')
            ->willReturn('eng-GB');

        $mapper->mapFieldValueForm($this->fieldForm, $this->data);
    }
}
