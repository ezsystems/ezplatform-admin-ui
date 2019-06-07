<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Tests\RepositoryForms\Data;

use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\ContentTranslationData;
use PHPUnit\Framework\TestCase;
use EzSystems\RepositoryForms\Data\Content\FieldData;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;

class ContentTranslationDataTest extends TestCase
{
    /** @var ContentTranslationData */
    private $contentTranslationData;

    protected function setUp(): void
    {
        $this->contentTranslationData = new ContentTranslationData();
    }

    public function testAddFieldData()
    {
        $this->assertNull($this->contentTranslationData->fieldsData);

        $this->contentTranslationData->addFieldData(new FieldData([
            'fieldDefinition' => $this->getFieldDefinition(),
        ]));

        $this->assertCount(1, $this->contentTranslationData->fieldsData);

        // Add another field with same identifier
        $this->contentTranslationData->addFieldData(new FieldData([
            'fieldDefinition' => $this->getFieldDefinition(),
        ]));
        $this->assertCount(1, $this->contentTranslationData->fieldsData);

        // Add field with another identifier
        $this->contentTranslationData->addFieldData(new FieldData([
            'fieldDefinition' => $this->getFieldDefinition('another_identifier'),
        ]));
        $this->assertCount(2, $this->contentTranslationData->fieldsData);
    }

    /**
     * @param string $identifier
     *
     * @return FieldDefinition
     */
    private function getFieldDefinition(string $identifier = 'identifier'): FieldDefinition
    {
        return new FieldDefinition([
            'identifier' => $identifier,
        ]);
    }
}
