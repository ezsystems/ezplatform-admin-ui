<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Validator\Constraints;

use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;

class LocationCopySubtreeValidationMessages implements TranslationContainerInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getTranslationMessages()
    {
        return [
            Message::create('ezplatform.copy_subtree.source_equal_to_target', 'validators')
                ->setDesc('Source Location can not be equal to target one'),
        ];
    }
}
