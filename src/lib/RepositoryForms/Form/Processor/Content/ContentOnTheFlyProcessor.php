<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\RepositoryForms\Form\Processor\Content;

use eZ\Publish\API\Repository\ContentService;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Dispatcher\ContentOnTheFlyDispatcher;
use EzSystems\RepositoryForms\Event\FormActionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ContentOnTheFlyProcessor implements EventSubscriberInterface
{
    /** @var ContentService */
    private $contentService;

    /** @var Environment */
    private $twig;

    /**
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \Twig\Environment $twig
     */
    public function __construct(ContentService $contentService, Environment $twig)
    {
        $this->contentService = $contentService;
        $this->twig = $twig;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            ContentOnTheFlyDispatcher::EVENT_BASE_NAME . '.publish' => ['processPublish', 10],
        ];
    }

    /**
     * @param \EzSystems\RepositoryForms\Event\FormActionEvent $event
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\ContentFieldValidationException
     * @throws \eZ\Publish\API\Repository\Exceptions\ContentValidationException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function processPublish(FormActionEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        $languageCode = $form->getConfig()->getOption('languageCode');
        $mainLanguageCode = $data->mainLanguageCode;

        foreach ($data->fieldsData as $fieldDefIdentifier => $fieldData) {
            if ($mainLanguageCode != $languageCode && !$fieldData->fieldDefinition->isTranslatable) {
                continue;
            }

            $data->setField($fieldDefIdentifier, $fieldData->value, $languageCode);
        }

        $draft = $this->contentService->createContent($data, $data->getLocationStructs());
        $versionInfo = $draft->versionInfo;
        $content = $this->contentService->publishVersion($versionInfo, [$versionInfo->initialLanguageCode]);

        $event->setResponse(
            new Response(
                $this->twig->render('@ezdesign/content/content_on_the_fly/content_create_response.html.twig', [
                    'locationId' => $content->contentInfo->mainLocationId,
                ])
            )
        );
    }
}
