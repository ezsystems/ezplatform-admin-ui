<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\EventListener;

use Symfony\Component\Form\FormEvent;

class SelectionMultilingualOptionsDataListener
{
    /** @var string */
    protected $languageCode;

    /**
     * @param string $languageCode
     */
    public function __construct(string $languageCode)
    {
        $this->languageCode = $languageCode;
    }

    /**
     * @param \Symfony\Component\Form\FormEvent $event
     */
    public function setLanguageOptions(FormEvent $event): void
    {
        $data = $event->getData();
        $event->setData($data[$this->languageCode] ?? []);
    }
}
