<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
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
class LocationIsNotRoot extends Constraint implements TranslationContainerInterface
{
    public $message = 'ezplatform.copy_subtree.is_root';

    public static function getTranslationMessages()
    {
        return [
            Message::create('ezplatform.copy_subtree.is_root', 'validators')
                ->setDesc('Selected Location can not be root Location.'),
        ];
    }
}
