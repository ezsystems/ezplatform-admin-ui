<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UniqueSectionIdentifier extends Constraint
{
    /**
     * %identifier% placeholder is passed.
     *
     * @var string
     */
    public $message = 'ez.section.identifier.unique';

    public function validatedBy()
    {
        return 'ezplatform.content_forms.validator.unique_section_identifier';
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
