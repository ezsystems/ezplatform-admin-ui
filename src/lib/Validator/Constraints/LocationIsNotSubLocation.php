<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Validator\Constraints;

use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Validator\Constraints\AbstractComparison;

/**
 * @Annotation
 */
class LocationIsNotSubLocation extends AbstractComparison implements TranslationContainerInterface
{
    public $message = 'ezplatform.copy_subtree.is_sub_location';

    public static function getTranslationMessages()
    {
        return [
            Message::create('ezplatform.copy_subtree.is_sub_location', 'validators')
                ->setDesc('Cannot copy subtree to its own descendant Location'),
        ];
    }
}
