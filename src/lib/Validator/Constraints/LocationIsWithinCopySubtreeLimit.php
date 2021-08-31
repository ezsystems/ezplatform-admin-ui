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
class LocationIsWithinCopySubtreeLimit extends Constraint implements TranslationContainerInterface
{
    public $message = 'ezplatform.copy_subtree.limit_exceeded';

    public static function getTranslationMessages()
    {
        return [
            Message::create('ezplatform.copy_subtree.limit_exceeded', 'validators')
                ->setDesc('Copy subtree limit exceeded. Current limit: %currentLimit%'),
        ];
    }
}
