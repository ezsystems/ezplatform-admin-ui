<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Processor\Content;

use EzSystems\EzPlatformAdminUi\Event\ContentOnTheFlyEvents;
use EzSystems\EzPlatformContentForms\Event\FormActionEvent;
use EzSystems\EzPlatformContentForms\Form\Processor\ContentFormProcessor;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ContentOnTheFlyProcessor implements EventSubscriberInterface
{
    /** @var \Twig\Environment */
    private $twig;

    /** @var \EzSystems\EzPlatformContentForms\Form\Processor\ContentFormProcessor */
    private $innerContentFormProcessor;

    public function __construct(
        Environment $twig,
        ContentFormProcessor $innerContentFormProcessor
    ) {
        $this->twig = $twig;
        $this->innerContentFormProcessor = $innerContentFormProcessor;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            ContentOnTheFlyEvents::CONTENT_CREATE_PUBLISH => ['processCreatePublish', 10],
            ContentOnTheFlyEvents::CONTENT_EDIT_PUBLISH => ['processEditPublish', 10],
        ];
    }

    /**
     * @param \EzSystems\EzPlatformContentForms\Event\FormActionEvent $event
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\ContentFieldValidationException
     * @throws \eZ\Publish\API\Repository\Exceptions\ContentValidationException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function processCreatePublish(FormActionEvent $event)
    {
        // Rely on Content Form Processor from ContentForms to avoid unncessary code duplication
        $this->innerContentFormProcessor->processPublish($event);

        /** @var \eZ\Publish\API\Repository\Values\Content\Content $content */
        $content = $event->getPayload('content');
        $referrerLocation = $event->getOption('referrerLocation');
        $locationId = $referrerLocation ? $referrerLocation->id : $content->contentInfo->mainLocationId;

        // We only need to change the response so it's compatible with UDW
        $event->setResponse(
            new Response(
                $this->twig->render('@ezdesign/ui/on_the_fly/content_create_response.html.twig', [
                    'locationId' => $locationId,
                ])
            )
        );
    }

    public function processEditPublish(FormActionEvent $event): void
    {
        // Rely on Content Form Processor from ContentForms to avoid unncessary code duplication
        $this->innerContentFormProcessor->processPublish($event);

        /** @var \eZ\Publish\API\Repository\Values\Content\Content $content */
        $content = $event->getPayload('content');
        $referrerLocation = $event->getOption('referrerLocation');
        $locationId = $referrerLocation ? $referrerLocation->id : $content->contentInfo->mainLocationId;

        // We only need to change the response so it's compatible with UDW
        $event->setResponse(
            new Response(
                $this->twig->render('@ezdesign/ui/on_the_fly/content_edit_response.html.twig', [
                    'locationId' => $locationId,
                ])
            )
        );
    }
}
