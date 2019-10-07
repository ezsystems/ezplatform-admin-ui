<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Suppresses validation on cancel button submit.
 */
class SuppressValidationSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::POST_SUBMIT => ['suppressValidationOnCancel', 900],
        ];
    }

    public function suppressValidationOnCancel(FormEvent $event)
    {
        $form = $event->getForm();

        if ($form->get('cancel')->isClicked()) {
            $event->stopPropagation();
        }
    }
}
