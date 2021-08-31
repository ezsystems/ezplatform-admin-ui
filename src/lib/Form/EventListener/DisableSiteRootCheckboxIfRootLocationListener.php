<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\EventListener;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormEvent;

class DisableSiteRootCheckboxIfRootLocationListener
{
    /**
     * @param \Symfony\Component\Form\FormEvent $event
     */
    public function onPreSetData(FormEvent $event)
    {
        $location = $event->getData()->getLocation();
        if (null !== $location && 1 >= $location->depth) {
            $form = $event->getForm();
            $form->add(
                'site_root',
                CheckboxType::class,
                [
                    'required' => false,
                    'label' => false,
                    'disabled' => true,
                ]
            );
        }
    }
}
