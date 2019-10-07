<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace EzSystems\EzPlatformAdminUi\FieldType;

use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Content\FieldData;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\FieldDefinitionData;
use Symfony\Component\Form\FormInterface;

/**
 * FieldType mappers dispatcher. Maps Field (definition, value) data to a Form using the appropriate mapper.
 */
interface FieldTypeFormMapperDispatcherInterface
{
    /**
     * Adds a new Field mapper for a fieldtype identifier.
     *
     * @param \EzSystems\EzPlatformAdminUi\FieldType\FieldFormMapperInterface
     * @param string $fieldTypeIdentifier fieldType identifier this mapper is for
     *
     * @return mixed
     */
    public function addMapper(FieldFormMapperInterface $mapper, $fieldTypeIdentifier);

    /**
     * Maps, if a mapper is available for the fieldtype, $data to $form.
     *
     * @param \Symfony\Component\Form\FormInterface $form
     * @param FieldData|FieldDefinitionData $data
     *
     * @return self
     *
     * @throws \InvalidArgumentException If $data is not a FieldData or FieldDefinitionData
     */
    public function map(FormInterface $form, $data);
}
