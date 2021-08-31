<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab;

use EzSystems\EzPlatformAdminUi\Tab\Event\TabEvents;
use EzSystems\EzPlatformAdminUi\Tab\Event\TabViewRenderEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * Base class representing Tab using EventDisaptcher for extensibility.
 *
 * It extends AbstractTab by adding Event Dispatching before rendering view.
 */
abstract class AbstractEventDispatchingTab extends AbstractTab
{
    /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface */
    protected $eventDispatcher;

    /**
     * @param \Twig\Environment $twig
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct($twig, $translator);

        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @inheritdoc
     */
    public function renderView(array $parameters): string
    {
        $event = new TabViewRenderEvent(
            $this->getIdentifier(),
            $this->getTemplate(),
            $this->getTemplateParameters($parameters)
        );
        $this->eventDispatcher->dispatch($event, TabEvents::TAB_RENDER);

        return $this->twig->render(
            $event->getTemplate(),
            $event->getParameters()
        );
    }

    /**
     * @return string
     */
    abstract public function getTemplate(): string;

    /**
     * @param mixed[] $contextParameters
     *
     * @return mixed[]
     */
    abstract public function getTemplateParameters(array $contextParameters = []): array;
}
