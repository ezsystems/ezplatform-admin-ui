<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace EzSystems\EzPlatformAdminUi\FieldType;

use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Content\FieldData;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\FieldDefinitionData;
use InvalidArgumentException;
use Symfony\Component\Form\FormInterface;

/**
 * FieldType mappers dispatcher.
 *
 * Adds the form elements matching the given Field Data (Value or Definition) to a given Form.
 */
class FieldTypeFormMapperDispatcher implements FieldTypeFormMapperDispatcherInterface
{
    /**
     * FieldType form mappers, indexed by FieldType identifier.
     *
     * @var FieldDefinitionFormMapperInterface[]|FieldValueFormMapperInterface[]
     */
    private $mappers = [];

    /**
     * @var FieldDefinitionFormMapperInterface[]
     */
    private $definitionMappers = [];

    /**
     * @var FieldValueFormMapperInterface[]
     */
    private $valueMappers = [];

    public function addMapper(FieldFormMapperInterface $mapper, $fieldTypeIdentifier)
    {
        if ($mapper instanceof FieldValueFormMapperInterface) {
            $this->valueMappers[$fieldTypeIdentifier] = $mapper;
            $valid = true;
        }

        if ($mapper instanceof FieldDefinitionFormMapperInterface) {
            $this->definitionMappers[$fieldTypeIdentifier] = $mapper;
            $valid = true;
        }

        if (!isset($valid)) {
            throw new \InvalidArgumentException(
                'Expecting a FieldValueFormMapperInterface, FieldDefinitionFormMapperInterface'
            );
        }
    }

    public function map(FormInterface $fieldForm, $data)
    {
        if (!$data instanceof FieldDefinitionData && !$data instanceof FieldData) {
            throw new InvalidArgumentException('Invalid data object, valid types are FieldData and FieldDefinitionData');
        }

        $fieldTypeIdentifier = $data->getFieldTypeIdentifier();

        if ($data instanceof FieldDefinitionData) {
            if (isset($this->definitionMappers[$fieldTypeIdentifier])) {
                $this->definitionMappers[$fieldTypeIdentifier]->mapFieldDefinitionForm($fieldForm, $data);
            } elseif (
                isset($this->mappers[$fieldTypeIdentifier]) &&
                $this->definitionMappers[$fieldTypeIdentifier] instanceof FieldDefinitionFormMapperInterface
            ) {
                $this->mappers[$fieldTypeIdentifier]->mapFieldDefinitionForm($fieldForm, $data);
            }

            return;
        }

        if ($data instanceof FieldData) {
            if (isset($this->valueMappers[$fieldTypeIdentifier])) {
                $this->valueMappers[$fieldTypeIdentifier]->mapFieldValueForm($fieldForm, $data);
            } elseif (
                isset($this->mappers[$fieldTypeIdentifier]) &&
                $this->mappers[$fieldTypeIdentifier] instanceof FieldValueFormMapperInterface
            ) {
                $this->mappers[$fieldTypeIdentifier]->mapFieldValueForm($fieldForm, $data);
            }

            return;
        }
    }
}
