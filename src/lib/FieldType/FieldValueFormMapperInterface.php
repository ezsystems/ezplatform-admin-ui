<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace EzSystems\EzPlatformAdminUi\FieldType;

use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Content\FieldData;
use Symfony\Component\Form\FormInterface;

interface FieldValueFormMapperInterface extends FieldFormMapperInterface
{
    /**
     * Maps Field form to current FieldType.
     * Allows to add form fields for content edition.
     *
     * @param FormInterface $fieldForm form for the current Field
     * @param FieldData $data underlying data for current Field form
     */
    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data);
}
