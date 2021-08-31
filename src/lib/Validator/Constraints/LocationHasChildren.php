<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Validator\Constraints;

use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class LocationHasChildren extends Constraint implements TranslationContainerInterface
{
    public $message = 'ezplatform.trash.location_has_no_children';

    public static function getTranslationMessages()
    {
        return [
            Message::create('ezplatform.trash.location_has_no_children', 'validators')
                ->setDesc('Selected Location has no children.'),
        ];
    }
}
