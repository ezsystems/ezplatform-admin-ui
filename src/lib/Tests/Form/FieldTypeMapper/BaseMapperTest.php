<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Tests\Form\FieldTypeMapper;

use eZ\Publish\API\Repository\FieldType;
use eZ\Publish\API\Repository\FieldTypeService;
use EzSystems\EzPlatformContentForms\Data\Content\FieldData;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

abstract class BaseMapperTest extends TestCase
{
    protected $fieldTypeService;
    protected $config;
    protected $fieldForm;
    protected $data;

    protected function setUp(): void
    {
        $this->fieldTypeService = $this->getMockBuilder(FieldTypeService::class)
            ->getMock();
        $this->fieldTypeService
            ->expects($this->any())
            ->method('getFieldType')
            ->willReturn($this->getMockBuilder(FieldType::class)->getMock());

        $this->config = $this->getMockBuilder(FormConfigInterface::class)->getMock();

        $formFactory = $this->getMockBuilder(FormFactoryInterface::class)
            ->setMethods(['addModelTransformer', 'setAutoInitialize', 'getForm'])
            ->getMockForAbstractClass();
        $formFactory->expects($this->once())
            ->method('createBuilder')
            ->willReturn($formFactory);
        $formFactory->expects($this->once())
            ->method('create')
            ->willReturn($formFactory);
        $formFactory->expects($this->once())
            ->method('addModelTransformer')
            ->willReturn($formFactory);
        $formFactory->expects($this->once())
            ->method('setAutoInitialize')
            ->willReturn($formFactory);

        $this->config->expects($this->once())
            ->method('getFormFactory')
            ->willReturn($formFactory);

        $this->fieldForm = $this->getMockBuilder(FormInterface::class)->getMock();
        $this->fieldForm->expects($this->once())
            ->method('getConfig')
            ->willReturn($this->config);

        $this->data = $this->getMockBuilder(FieldData::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
