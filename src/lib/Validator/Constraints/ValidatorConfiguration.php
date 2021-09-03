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
class ValidatorConfiguration extends Constraint
{
    public $message = 'ez.field_definition.validator_configuration';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy()
    {
        return 'ezplatform.content_forms.validator.validator_configuration';
    }
}

class_alias(ValidatorConfiguration::class, 'EzSystems\EzPlatformAdminUi\Validator\Constraints\ValidatorConfiguration');
