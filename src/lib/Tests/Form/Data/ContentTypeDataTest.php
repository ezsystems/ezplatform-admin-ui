<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Tests\Form\Data;

use eZ\Publish\API\Repository\Values\ContentType\ContentTypeDraft;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use EzSystems\EzPlatformAdminUi\Form\Data\ContentTypeData;
use EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionData;
use PHPUnit\Framework\TestCase;

class ContentTypeDataTest extends TestCase
{
    public function testContentTypeDraft(): void
    {
        $contentTypeDraft = $this->getMockForAbstractClass(ContentTypeDraft::class);
        $data = new ContentTypeData(['contentTypeDraft' => $contentTypeDraft]);
        self::assertSame($contentTypeDraft, $data->contentTypeDraft);
    }

    public function testFieldDefinitionData(): void
    {
        $fieldDef1 = new FieldDefinitionData([
            'fieldGroup' => 'field_group__alpha',
            'identifier' => 'identifier__alpha',
        ]);
        $fieldDef2 = new FieldDefinitionData([
            'fieldGroup' => 'field_group__bravo',
            'identifier' => 'identifier__bravo',
        ]);
        $fieldDef3 = new FieldDefinitionData([
            'fieldGroup' => 'field_group__charlie',
            'identifier' => 'identifier__charlie',
        ]);
        $fieldDef4 = new FieldDefinitionData([
            'fieldGroup' => 'field_group__delta',
            'identifier' => 'identifier__delta',
        ]);

        $initialFieldDefs = [
            'field_group__alpha' => ['identifier__alpha' => $fieldDef1],
            'field_group__bravo' => ['identifier__bravo' => $fieldDef2],
        ];
        $data = new ContentTypeData([
            'fieldDefinitionsData' => $initialFieldDefs,
        ]);
        self::assertSame($initialFieldDefs, $data->fieldDefinitionsData);

        $data->addFieldDefinitionData($fieldDef3);
        $data->addFieldDefinitionData($fieldDef4);
        self::assertSame([
            'field_group__alpha' => ['identifier__alpha' => $fieldDef1],
            'field_group__bravo' => ['identifier__bravo' => $fieldDef2],
            'field_group__charlie' => ['identifier__charlie' => $fieldDef3],
            'field_group__delta' => ['identifier__delta' => $fieldDef4],
        ], $data->fieldDefinitionsData);

        self::assertSame([
            'field_group__alpha.identifier__alpha' => $fieldDef1,
            'field_group__bravo.identifier__bravo' => $fieldDef2,
            'field_group__charlie.identifier__charlie' => $fieldDef3,
            'field_group__delta.identifier__delta' => $fieldDef4,
        ], iterator_to_array($data->getFlatFieldDefinitionsData()));
    }

    public function testSortFieldDefinitions(): void
    {
        $fieldDef1 = new FieldDefinitionData([
            'fieldDefinition' => $this->getMockForAbstractClass(
                FieldDefinition::class,
                [['id' => 1, 'identifier' => 'snarf', 'position' => 3]]
            ),
            'fieldGroup' => 'foo',
        ]);
        $fieldDef2 = new FieldDefinitionData([
            'fieldDefinition' => $this->getMockForAbstractClass(
                FieldDefinition::class,
                [['id' => 2, 'identifier' => 'gnubel', 'position' => 2]]
            ),
            'fieldGroup' => 'foo',
        ]);
        $fieldDef3 = new FieldDefinitionData([
            'fieldDefinition' => $this->getMockForAbstractClass(
                FieldDefinition::class,
                [['id' => 3, 'identifier' => 'heffa', 'position' => 2]]
            ),
            'fieldGroup' => 'foo',
        ]);
        $fieldDef4 = new FieldDefinitionData([
            'fieldDefinition' => $this->getMockForAbstractClass(
                FieldDefinition::class,
                [['id' => 4, 'identifier' => 'lump', 'position' => 1]]
            ),
            'fieldGroup' => 'foo',
        ]);

        $fieldDefs = [
            'foo' => [
                'snarf' => $fieldDef1,
                'gnubel' => $fieldDef2,
                'heffa' => $fieldDef3,
                'lump' => $fieldDef4,
            ],
        ];
        $data = new ContentTypeData(['fieldDefinitionsData' => $fieldDefs]);
        self::assertSame($fieldDefs, $data->fieldDefinitionsData);

        $data->sortFieldDefinitions();
        self::assertSame([
            'foo' => [
                'lump' => $fieldDef4,
                'gnubel' => $fieldDef2,
                'heffa' => $fieldDef3,
                'snarf' => $fieldDef1,
            ],
        ], $data->fieldDefinitionsData);
    }
}
