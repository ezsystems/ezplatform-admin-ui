<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\FieldType;

use EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionData;
use Symfony\Component\Form\FormInterface;

/**
 * FieldType mappers dispatcher. Maps Field Definition data to a Form using the appropriate mapper.
 */
interface FieldTypeDefinitionFormMapperDispatcherInterface
{
    public function addMapper(FieldDefinitionFormMapperInterface $mapper, string $fieldTypeIdentifier): void;

    public function map(FormInterface $form, FieldDefinitionData $data): void;
}
