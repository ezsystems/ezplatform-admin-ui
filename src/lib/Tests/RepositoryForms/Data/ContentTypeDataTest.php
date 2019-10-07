<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace EzSystems\EzPlatformAdminUi\Tests\RepositoryForms\Data;

use eZ\Publish\API\Repository\Values\ContentType\ContentTypeDraft;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\ContentTypeData;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\FieldDefinitionData;
use PHPUnit\Framework\TestCase;

class ContentTypeDataTest extends TestCase
{
    public function testContentTypeDraft()
    {
        $contentTypeDraft = $this->getMockForAbstractClass(ContentTypeDraft::class);
        $data = new ContentTypeData(['contentTypeDraft' => $contentTypeDraft]);
        self::assertSame($contentTypeDraft, $data->contentTypeDraft);
    }

    public function testFieldDefinitionData()
    {
        $fieldDef1 = new FieldDefinitionData();
        $fieldDef2 = new FieldDefinitionData();
        $fieldDef3 = new FieldDefinitionData();
        $fieldDef4 = new FieldDefinitionData();

        $initialFieldDefs = [$fieldDef1, $fieldDef2];
        $data = new ContentTypeData(['fieldDefinitionsData' => $initialFieldDefs]);
        self::assertSame($initialFieldDefs, $data->fieldDefinitionsData);

        $data->addFieldDefinitionData($fieldDef3);
        $data->addFieldDefinitionData($fieldDef4);
        self::assertSame([$fieldDef1, $fieldDef2, $fieldDef3, $fieldDef4], $data->fieldDefinitionsData);
    }

    public function testSortFieldDefinitions()
    {
        $fieldDef1 = new FieldDefinitionData(
            ['fieldDefinition' => $this->getMockForAbstractClass(
                FieldDefinition::class,
                [['id' => 1, 'identifier' => 'snarf', 'position' => 3]]
            )]
        );
        $fieldDef2 = new FieldDefinitionData(
            ['fieldDefinition' => $this->getMockForAbstractClass(
                FieldDefinition::class,
                [['id' => 2, 'identifier' => 'gnubel', 'position' => 2]]
            )]
        );
        $fieldDef3 = new FieldDefinitionData(
            ['fieldDefinition' => $this->getMockForAbstractClass(
                FieldDefinition::class,
                [['id' => 3, 'identifier' => 'heffa', 'position' => 2]]
            )]
        );
        $fieldDef4 = new FieldDefinitionData(
            ['fieldDefinition' => $this->getMockForAbstractClass(
                FieldDefinition::class,
                [['id' => 4, 'identifier' => 'lump', 'position' => 1]]
            )]
        );

        $fieldDefs = [$fieldDef1, $fieldDef2, $fieldDef3, $fieldDef4];
        $data = new ContentTypeData(['fieldDefinitionsData' => $fieldDefs]);
        self::assertSame($fieldDefs, $data->fieldDefinitionsData);

        $data->sortFieldDefinitions();
        self::assertSame([$fieldDef4, $fieldDef2, $fieldDef3, $fieldDef1], $data->fieldDefinitionsData);
    }
}
