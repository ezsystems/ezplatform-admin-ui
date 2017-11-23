<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Processor;

use Exception;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Exceptions\BadStateException;
use eZ\Publish\API\Repository\Exceptions\ContentFieldValidationException;
use eZ\Publish\API\Repository\Exceptions\ContentValidationException;
use eZ\Publish\API\Repository\Exceptions\InvalidArgumentException;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\ContentStruct;
use EzSystems\EzPlatformAdminUi\Form\Event\ContentEditEvents;
use EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface;
use EzSystems\RepositoryForms\Data\Content\ContentCreateData;
use EzSystems\RepositoryForms\Data\Content\ContentData;
use EzSystems\RepositoryForms\Data\Content\ContentUpdateData;
use EzSystems\RepositoryForms\Event\FormActionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Listens for and processes RepositoryForm events.
 */
class ContentFormProcessor implements EventSubscriberInterface
{
    /** @var ContentService */
    private $contentService;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /** @var NotificationHandlerInterface */
    private $notificationHandler;

    /** @var TranslatorInterface */
    private $translator;

    /**
     * @param ContentService $contentService
     * @param UrlGeneratorInterface $urlGenerator
     * @param NotificationHandlerInterface $notificationHandler
     * @param TranslatorInterface $translator
     */
    public function __construct(
        ContentService $contentService,
        UrlGeneratorInterface $urlGenerator,
        NotificationHandlerInterface $notificationHandler,
        TranslatorInterface $translator
    ) {
        $this->contentService = $contentService;
        $this->urlGenerator = $urlGenerator;
        $this->notificationHandler = $notificationHandler;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ContentEditEvents::CONTENT_PREVIEW => ['processPreview', 10],
        ];
    }

    /**
     * @param FormActionEvent $event
     *
     * @throws \InvalidArgumentException
     */
    public function processPreview(FormActionEvent $event): void
    {
        /** @var ContentCreateData|ContentUpdateData $data */
        $data = $event->getData();
        $form = $event->getForm();
        $languageCode = $form->getConfig()->getOption('languageCode');

        try {
            $contentDraft = $this->saveDraft($data, $languageCode);
            $url = $this->urlGenerator->generate('ezplatform.content.preview', [
                'contentId' => $contentDraft->id,
                'versionNo' => $contentDraft->getVersionInfo()->versionNo,
                'languageCode' => $languageCode,
            ]);
        } catch (Exception $e) {
            $this->notificationHandler->error(
                /** @Desc("Cannot save content draft.") */
                $this->translator->trans(
                    'error.preview',
                    [],
                    'content_preview'
                )
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
     * @param ContentStruct|ContentCreateData|ContentUpdateData $data
     * @param string $languageCode
     *
     * @return Content
     *
     * @throws BadStateException
     * @throws UnauthorizedException
     * @throws InvalidArgumentException
     * @throws ContentValidationException
     * @throws ContentFieldValidationException
     */
    private function saveDraft(ContentStruct $data, string $languageCode): Content
    {
        foreach ($data->fieldsData as $fieldDefIdentifier => $fieldData) {
            $data->setField($fieldDefIdentifier, $fieldData->value, $languageCode);
        }

        if ($data->isNew()) {
            $contentDraft = $this->contentService->createContent($data, $data->getLocationStructs());
        } else {
            $contentDraft = $this->contentService->updateContent($data->contentDraft->getVersionInfo(), $data);
        }

        return $contentDraft;
    }

    /**
     * Returns content create or edit URL depending on $data type.
     *
     * @param ContentData|ContentCreateData|ContentUpdateData $data
     * @param string $languageCode
     *
     * @return string
     *
     * @throws RouteNotFoundException
     * @throws MissingMandatoryParametersException
     * @throws InvalidParameterException
     */
    private function getContentEditUrl(ContentData $data, string $languageCode): string
    {
        return $data->isNew()
            ? $this->urlGenerator->generate('ez_content_create_no_draft', [
                'parentLocationId' => $data->getLocationStructs()[0]->parentLocationId,
                'contentTypeIdentifier' => $data->contentType->identifier,
                'language' => $languageCode,
            ])
            : $this->urlGenerator->generate('ez_content_draft_edit', [
                'contentId' => $data->contentDraft->id,
                'versionNo' => $data->contentDraft->getVersionInfo()->versionNo,
                'language' => $languageCode,
            ]);
    }
}
