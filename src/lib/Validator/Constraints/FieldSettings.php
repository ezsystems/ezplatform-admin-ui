<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\AdminUi\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class FieldSettings extends Constraint
{
    public $message = 'ez.field_definition.field_settings';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy()
    {
        return 'ezplatform.content_forms.validator.field_settings';
    }
}

class_alias(FieldSettings::class, 'EzSystems\EzPlatformAdminUi\Validator\Constraints\FieldSettings');
