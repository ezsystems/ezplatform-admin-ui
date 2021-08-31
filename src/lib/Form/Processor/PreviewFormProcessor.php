<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Processor;

use Exception;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\ContentStruct;
use eZ\Publish\API\Repository\Values\Content\Location;
use EzSystems\EzPlatformAdminUi\Form\Event\ContentEditEvents;
use EzSystems\EzPlatformAdminUi\Notification\TranslatableNotificationHandlerInterface;
use EzSystems\EzPlatformContentForms\Data\NewnessCheckable;
use EzSystems\EzPlatformContentForms\Event\FormActionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Listens for and processes RepositoryForm events.
 */
class PreviewFormProcessor implements EventSubscriberInterface
{
    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface */
    private $urlGenerator;

    /** @var \EzSystems\EzPlatformAdminUi\Notification\TranslatableNotificationHandlerInterface */
    private $notificationHandler;

    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    /**
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \Symfony\Component\Routing\Generator\UrlGeneratorInterface $urlGenerator
     * @param \EzSystems\EzPlatformAdminUi\Notification\TranslatableNotificationHandlerInterface $notificationHandler
     * @param \eZ\Publish\API\Repository\LocationService $locationService
     */
    public function __construct(
        ContentService $contentService,
        UrlGeneratorInterface $urlGenerator,
        TranslatableNotificationHandlerInterface $notificationHandler,
        LocationService $locationService
    ) {
        $this->contentService = $contentService;
        $this->urlGenerator = $urlGenerator;
        $this->notificationHandler = $notificationHandler;
        $this->locationService = $locationService;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ContentEditEvents::CONTENT_PREVIEW => ['processPreview', 10],
        ];
    }

    /**
     * @param \EzSystems\EzPlatformContentForms\Event\FormActionEvent $event
     *
     * @throws \InvalidArgumentException
     */
    public function processPreview(FormActionEvent $event): void
    {
        /** @var \EzSystems\EzPlatformContentForms\Data\Content\ContentCreateData|\EzSystems\EzPlatformContentForms\Data\Content\ContentUpdateData $data */
        $data = $event->getData();
        $form = $event->getForm();
        $languageCode = $form->getConfig()->getOption('languageCode');
        $referrerLocation = $event->getOption('referrerLocation');

        try {
            $contentDraft = $this->saveDraft($data, $languageCode, []);
            $contentLocation = $this->resolveLocation($contentDraft, $referrerLocation, $data);
            $url = $this->urlGenerator->generate('ezplatform.content.preview', [
                'locationId' => null !== $contentLocation ? $contentLocation->id : null,
                'contentId' => $contentDraft->id,
                'versionNo' => $contentDraft->getVersionInfo()->versionNo,
                'languageCode' => $languageCode,
            ]);
        } catch (Exception $e) {
            $this->notificationHandler->error(
                /** @Desc("Cannot save content draft.") */
                'error.preview',
                [],
                'content_preview'
            );
            $url = $this->getContentEditUrl($data, $languageCode);
        }

        $event->setResponse(
            new RedirectResponse($url)
        );
    }

    /**
     * Saves content draft corresponding to $data.
     * Depending on the nature of $data (create or update data), the draft will either be created or simply updated.
     *
     * @param \EzSystems\EzPlatformContentForms\Data\Content\ContentCreateData|\eZ\Publish\API\Repository\Values\Content\ContentStruct|\EzSystems\EzPlatformContentForms\Data\Content\ContentUpdateData $data
     * @param string $languageCode
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Content
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\ContentValidationException
     * @throws \eZ\Publish\API\Repository\Exceptions\ContentFieldValidationException
     */
    private function saveDraft(ContentStruct $data, string $languageCode, ?array $fieldIdentifiersToValidate): Content
    {
        $mainLanguageCode = $this->resolveMainLanguageCode($data);
        foreach ($data->fieldsData as $fieldDefIdentifier => $fieldData) {
            if ($mainLanguageCode != $languageCode && !$fieldData->fieldDefinition->isTranslatable) {
                continue;
            }

            $data->setField($fieldDefIdentifier, $fieldData->value, $languageCode);
        }

        if ($data->isNew()) {
            $contentDraft = $this->contentService->createContent($data, $data->getLocationStructs(), $fieldIdentifiersToValidate);
        } else {
            $contentDraft = $this->contentService->updateContent($data->contentDraft->getVersionInfo(), $data, $fieldIdentifiersToValidate);
        }

        return $contentDraft;
    }

    /**
     * Returns content create or edit URL depending on $data type.
     *
     * @param \EzSystems\EzPlatformContentForms\Data\Content\ContentCreateData|\EzSystems\EzPlatformContentForms\Data\Content\ContentUpdateData $data
     * @param string $languageCode
     *
     * @return string
     *
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     */
    private function getContentEditUrl($data, string $languageCode): string
    {
        return $data->isNew()
            ? $this->urlGenerator->generate('ezplatform.content.create.proxy', [
                'parentLocationId' => $data->getLocationStructs()[0]->parentLocationId,
                'contentTypeIdentifier' => $data->contentType->identifier,
                'languageCode' => $languageCode,
            ])
            : $this->urlGenerator->generate('ezplatform.content.draft.edit', [
                'contentId' => $data->contentDraft->id,
                'versionNo' => $data->contentDraft->getVersionInfo()->versionNo,
                'language' => $languageCode,
            ]);
    }

    /**
     * @param \EzSystems\EzPlatformContentForms\Data\Content\ContentCreateData|\EzSystems\EzPlatformContentForms\Data\Content\ContentUpdateData|\EzSystems\EzPlatformAdminUi\Form\Data\NewnessChecker $data
     *
     * @return string
     */
    private function resolveMainLanguageCode($data): string
    {
        return $data->isNew()
            ? $data->mainLanguageCode
            : $data->contentDraft->getVersionInfo()->getContentInfo()->mainLanguageCode;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Content $content
     * @param \eZ\Publish\API\Repository\Values\Content\Location|null $referrerLocation
     * @param \EzSystems\EzPlatformContentForms\Data\NewnessCheckable $data
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Location|null
     */
    private function resolveLocation(Content $content, ?Location $referrerLocation, NewnessCheckable $data): ?Location
    {
        if ($data->isNew() || (!$content->contentInfo->published && null === $content->contentInfo->mainLocationId)) {
            return null; // no location exists until new content is published
        }

        return $referrerLocation ?? $this->locationService->loadLocation($content->contentInfo->mainLocationId);
    }
}
