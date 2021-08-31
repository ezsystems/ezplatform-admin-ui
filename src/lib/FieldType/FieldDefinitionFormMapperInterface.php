<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\FieldType;

use EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionData;
use Symfony\Component\Form\FormInterface;

/**
 * A field definition mapper maps FieldDefinition to a FieldDefinitionForm.
 *
 * Each FieldType will implement its own, depending on the form elements it takes to configure a FieldDefinition.
 */
interface FieldDefinitionFormMapperInterface
{
    /**
     * "Maps" FieldDefinition form to current FieldType.
     * Gives the opportunity to enrich $fieldDefinitionForm with custom fields for:
     * - validator configuration,
     * - field settings
     * - default value.
     *
     * @param \Symfony\Component\Form\FormInterface $fieldDefinitionForm form for current FieldDefinition
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionData $data underlying data for current FieldDefinition form
     */
    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data): void;
}

class_alias(
    FieldDefinitionFormMapperInterface::class,
    \EzSystems\RepositoryForms\FieldType\FieldDefinitionFormMapperInterface::class
);
