<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\Processor;

use eZ\Publish\API\Repository\ContentService;
use EzSystems\EzPlatformAdminUi\Form\Data\ContentTranslationData;
use EzSystems\EzPlatformContentForms\Data\Content\ContentUpdateData;
use EzSystems\EzPlatformContentForms\Data\Content\FieldData;
use EzSystems\EzPlatformContentForms\Event\ContentFormEvents;
use EzSystems\EzPlatformContentForms\Event\FormActionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Listens for and processes RepositoryForm events: publish, remove draft, save draft...
 */
class TranslationFormProcessor implements EventSubscriberInterface
{
    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    public function __construct(
        ContentService $contentService
    ) {
        $this->contentService = $contentService;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ContentFormEvents::CONTENT_EDIT => ['createContentDraft', 20],
        ];
    }

    /**
     * Creates content draft based in data submitted by the user and injects ContentUpdateData to the event.
     *
     * This step is required to achieve compatibility with other FormProcessors.
     *
     * @param \EzSystems\EzPlatformContentForms\Event\FormActionEvent $event
     */
    public function createContentDraft(FormActionEvent $event): void
    {
        /** @var \EzSystems\EzPlatformAdminUi\Form\Data\ContentTranslationData $data */
        $data = $event->getData();

        if (!$data instanceof ContentTranslationData) {
            return;
        }

        $contentDraft = $this->contentService->createContentDraft($data->content->contentInfo);
        $fields = array_filter($data->fieldsData, static function (FieldData $fieldData) use ($contentDraft, $data) {
            $mainLanguageCode = $contentDraft->getVersionInfo()->getContentInfo()->mainLanguageCode;

            return $mainLanguageCode === $data->initialLanguageCode
                || ($mainLanguageCode !== $data->initialLanguageCode && $fieldData->fieldDefinition->isTranslatable);
        });
        $contentUpdateData = new ContentUpdateData([
            'initialLanguageCode' => $data->initialLanguageCode,
            'contentDraft' => $contentDraft,
            'fieldsData' => $fields,
        ]);

        $event->setData($contentUpdateData);
    }
}
