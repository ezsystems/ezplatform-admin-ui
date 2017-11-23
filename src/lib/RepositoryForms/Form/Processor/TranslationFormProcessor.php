<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\RepositoryForms\Form\Processor;

use eZ\Publish\API\Repository\ContentService;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\ContentTranslationData;
use EzSystems\RepositoryForms\Data\Content\ContentUpdateData;
use EzSystems\RepositoryForms\Data\Content\FieldData;
use EzSystems\RepositoryForms\Event\FormActionEvent;
use EzSystems\RepositoryForms\Event\RepositoryFormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Listens for and processes RepositoryForm events: publish, remove draft, save draft...
 */
class TranslationFormProcessor implements EventSubscriberInterface
{
    /** @var ContentService */
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
            RepositoryFormEvents::CONTENT_EDIT => ['createContentDraft', 20],
        ];
    }

    /**
     * Creates content draft based in data submitted by the user and injects ContentUpdateData to the event.
     *
     * This step is required to achieve compatibility with other FormProcessors.
     *
     * @param FormActionEvent $event
     */
    public function createContentDraft(FormActionEvent $event): void
    {
        /** @var ContentTranslationData $data */
        $data = $event->getData();

        if (!$data instanceof ContentTranslationData) {
            return;
        }

        $contentDraft = $this->contentService->createContentDraft($data->content->contentInfo);
        $fields = array_filter($data->fieldsData, function (FieldData $fieldData) use ($contentDraft, $data) {
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
