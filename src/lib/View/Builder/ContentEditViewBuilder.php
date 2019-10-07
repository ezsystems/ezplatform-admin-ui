<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\View\Builder;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface;
use eZ\Publish\Core\MVC\Symfony\View\Builder\ViewBuilder;
use eZ\Publish\Core\MVC\Symfony\View\Configurator;
use eZ\Publish\Core\MVC\Symfony\View\ParametersInjector;
use EzSystems\EzPlatformAdminUi\View\ContentEditSuccessView;
use EzSystems\EzPlatformAdminUi\View\ContentEditView;
use EzSystems\EzPlatformAdminUi\Form\ActionDispatcher\ActionDispatcherInterface;

/**
 * Builds ContentEditView objects.
 *
 * @internal
 */
class ContentEditViewBuilder implements ViewBuilder
{
    /** @var \eZ\Publish\API\Repository\Repository */
    private $repository;

    /** @var \eZ\Publish\Core\MVC\Symfony\View\Configurator */
    private $viewConfigurator;

    /** @var \eZ\Publish\Core\MVC\Symfony\View\ParametersInjector */
    private $viewParametersInjector;

    /** @var string */
    private $defaultTemplate;

    /** @var \EzSystems\EzPlatformAdminUi\Form\ActionDispatcher\ActionDispatcherInterface */
    private $contentActionDispatcher;

    /** @var \eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface */
    private $languagePreferenceProvider;

    /**
     * @param \eZ\Publish\API\Repository\Repository $repository
     * @param \eZ\Publish\Core\MVC\Symfony\View\Configurator $viewConfigurator
     * @param \eZ\Publish\Core\MVC\Symfony\View\ParametersInjector $viewParametersInjector
     * @param string $defaultTemplate
     * @param \EzSystems\EzPlatformAdminUi\Form\ActionDispatcher\ActionDispatcherInterface $contentActionDispatcher
     * @param \eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface $languagePreferenceProvider
     */
    public function __construct(
        Repository $repository,
        Configurator $viewConfigurator,
        ParametersInjector $viewParametersInjector,
        string $defaultTemplate,
        ActionDispatcherInterface $contentActionDispatcher,
        UserLanguagePreferenceProviderInterface $languagePreferenceProvider
    ) {
        $this->repository = $repository;
        $this->viewConfigurator = $viewConfigurator;
        $this->viewParametersInjector = $viewParametersInjector;
        $this->defaultTemplate = $defaultTemplate;
        $this->contentActionDispatcher = $contentActionDispatcher;
        $this->languagePreferenceProvider = $languagePreferenceProvider;
    }

    public function matches($argument)
    {
        return 'EzSystems\EzPlatformAdminUiBundle\Controller\ContentEditController::editVersionDraftAction' === $argument;
    }

    /**
     * @param array $parameters
     *
     * @return \eZ\Publish\Core\MVC\Symfony\View\ContentView|\eZ\Publish\Core\MVC\Symfony\View\View
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentType
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function buildView(array $parameters)
    {
        // @todo improve default templates injection
        $view = new ContentEditView($this->defaultTemplate);

        $language = $this->resolveLanguage($parameters);
        $location = $this->resolveLocation($parameters);
        $content = $this->resolveContent($parameters, $location, $language);
        $contentInfo = $content->contentInfo;
        $contentType = $this->loadContentType((int) $contentInfo->contentTypeId, $this->languagePreferenceProvider->getPreferredLanguages());
        $form = $parameters['form'];
        $isPublished = null !== $contentInfo->mainLocationId && $contentInfo->published;

        if (!$content->getVersionInfo()->isDraft()) {
            throw new InvalidArgumentException('Version', 'status is not draft');
        }

        if (null === $location && $isPublished) {
            // assume main location if no location was provided
            $location = $this->loadLocation((int) $contentInfo->mainLocationId);
        }

        if (null !== $location && $location->contentId !== $content->id) {
            throw new InvalidArgumentException('Location', 'Provided location does not belong to selected content');
        }

        if ($form->isSubmitted() && $form->isValid() && null !== $form->getClickedButton()) {
            $this->contentActionDispatcher->dispatchFormAction(
                $form,
                $form->getData(),
                $form->getClickedButton()->getName(),
                ['referrerLocation' => $location]
            );

            if ($response = $this->contentActionDispatcher->getResponse()) {
                $view = new ContentEditSuccessView($response);
                $view->setLocation($location);

                return $view;
            }
        }

        $view->setContent($content);
        $view->setLanguage($language);
        $view->setLocation($location);
        $view->setForm($parameters['form']);

        $view->addParameters([
            'content' => $content,
            'location' => $location,
            'language' => $language,
            'content_type' => $contentType,
            'form' => $form->createView(),
        ]);

        $this->viewParametersInjector->injectViewParameters($view, $parameters);
        $this->viewConfigurator->configure($view);

        return $view;
    }

    /**
     * Loads Content with id $contentId.
     *
     * @param int $contentId
     * @param array $languages
     * @param int|null $versionNo
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Content
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    private function loadContent(int $contentId, array $languages = [], int $versionNo = null): Content
    {
        return $this->repository->getContentService()->loadContent($contentId, $languages, $versionNo);
    }

    /**
     * Loads a visible Location.
     *
     * @param int $locationId
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Location
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    private function loadLocation(int $locationId): Location
    {
        return $this->repository->getLocationService()->loadLocation($locationId);
    }

    /**
     * Loads Language with code $languageCode.
     *
     * @param string $languageCode
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Language
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    private function loadLanguage(string $languageCode): Language
    {
        return $this->repository->getContentLanguageService()->loadLanguage($languageCode);
    }

    /**
     * Loads ContentType with id $contentTypeId.
     *
     * @param int $contentTypeId
     * @param string[] $languageCodes
     *
     * @return \eZ\Publish\API\Repository\Values\ContentType\ContentType
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    private function loadContentType(int $contentTypeId, array $languageCodes): ContentType
    {
        return $this->repository->getContentTypeService()->loadContentType($contentTypeId, $languageCodes);
    }

    /**
     * @param array $parameters
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Language
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    private function resolveLanguage(array $parameters): Language
    {
        if (isset($parameters['languageCode'])) {
            return $this->loadLanguage($parameters['languageCode']);
        }

        if (isset($parameters['language'])) {
            if (is_string($parameters['language'])) {
                // @todo BC: route parameter should be called languageCode but it won't happen until 3.0
                return $this->loadLanguage($parameters['language']);
            }

            return $parameters['language'];
        }

        throw new InvalidArgumentException('Language',
            'No language information provided. Are you missing language or languageCode parameters');
    }

    /**
     * @param array $parameters
     * @param \eZ\Publish\API\Repository\Values\Content\Location|null $location
     * @param \eZ\Publish\API\Repository\Values\Content\Language $language
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Content
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    private function resolveContent(array $parameters, ?Location $location, Language $language): Content
    {
        if (isset($parameters['content'])) {
            return $parameters['content'];
        }

        if (isset($parameters['contentId'])) {
            $contentId = $parameters['contentId'];
        } elseif (null !== $location) {
            $contentId = $location->contentId;
        } else {
            throw new InvalidArgumentException(
                'Content',
                'No content could be loaded from parameters'
            );
        }

        return $this->loadContent(
            (int) $contentId,
            null !== $language ? [$language->languageCode] : [],
            (int) $parameters['versionNo'] ?: null
        );
    }

    /**
     * @param array $parameters
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Location|null
     */
    private function resolveLocation(array $parameters): ?Location
    {
        if (isset($parameters['locationId'])) {
            return $this->loadLocation((int) $parameters['locationId']);
        }

        if (isset($parameters['location'])) {
            return $parameters['location'];
        }

        return null;
    }
}
