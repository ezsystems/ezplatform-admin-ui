<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Tests\FieldType;

use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use EzSystems\EzPlatformAdminUi\FieldType\FieldDefinitionFormMapperInterface;
use EzSystems\EzPlatformAdminUi\FieldType\FieldTypeDefinitionFormMapperDispatcher;
use EzSystems\EzPlatformAdminUi\Form\Data\ContentTypeData;
use EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionData;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormInterface;

class FieldTypeFormMapperDispatcherTest extends TestCase
{
    /**
     * @var \EzSystems\EzPlatformAdminUi\FieldType\FieldTypeDefinitionFormMapperDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var \EzSystems\EzPlatformAdminUi\FieldType\FieldDefinitionFormMapperInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $fieldDefinitionMapperMock;

    protected function setUp(): void
    {
        $this->dispatcher = new FieldTypeDefinitionFormMapperDispatcher();

        $this->fieldDefinitionMapperMock = $this->createMock(FieldDefinitionFormMapperInterface::class);
        $this->dispatcher->addMapper($this->fieldDefinitionMapperMock, 'first_type');
    }

    public function testMapFieldDefinition()
    {
        $data = new FieldDefinitionData([
            'fieldDefinition' => new FieldDefinition(['fieldTypeIdentifier' => 'first_type']),
            'contentTypeData' => new ContentTypeData(),
        ]);

        $formMock = $this->createMock(FormInterface::class);

        $this->fieldDefinitionMapperMock
            ->expects($this->once())
            ->method('mapFieldDefinitionForm')
            ->with($formMock, $data);

        $this->dispatcher->map($formMock, $data);
    }
}
