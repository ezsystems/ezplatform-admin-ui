<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\RepositoryForms\Form\Processor\ContentType;

use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\ContentTypeData;
use EzSystems\EzPlatformAdminUi\Event\FormActionEvent;
use EzSystems\EzPlatformAdminUi\Event\RepositoryFormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Listens for and processes RepositoryForm events.
 */
class ContentTypeDiscardChangesFormProcessor implements EventSubscriberInterface
{
    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /**
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            RepositoryFormEvents::CONTENT_TYPE_REMOVE_DRAFT => ['processDiscardChanges', 10],
        ];
    }

    public function processDiscardChanges(FormActionEvent $event)
    {
        /** @var ContentTypeData $data */
        $data = $event->getData();
        $contentTypeDraft = $data->contentTypeDraft;

        if (null === $contentTypeDraft || empty($contentTypeDraft->getContentTypeGroups())) {
            return;
        }

        /** @var $contentTypeGroup */
        $contentTypeGroup = $contentTypeDraft->getContentTypeGroups()[0];

        $event->setResponse(
            new RedirectResponse($this->urlGenerator->generate('ezplatform.content_type_group.view', [
                'contentTypeGroupId' => $contentTypeGroup->id,
            ]))
        );
    }
}
