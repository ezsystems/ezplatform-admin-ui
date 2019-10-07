<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\View\Builder;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface;
use eZ\Publish\Core\MVC\Symfony\View\Builder\ViewBuilder;
use eZ\Publish\Core\MVC\Symfony\View\Configurator;
use eZ\Publish\Core\MVC\Symfony\View\ParametersInjector;
use EzSystems\EzPlatformAdminUi\View\ContentCreateSuccessView;
use EzSystems\EzPlatformAdminUi\View\ContentCreateView;
use EzSystems\EzPlatformAdminUi\Form\ActionDispatcher\ActionDispatcherInterface;

/**
 * Builds ContentCreateView objects.
 *
 * @internal
 */
class ContentCreateViewBuilder implements ViewBuilder
{
    /** @var \eZ\Publish\API\Repository\Repository */
    private $repository;

    /** @var \eZ\Publish\Core\MVC\Symfony\View\Configurator */
    private $viewConfigurator;

    /** @var \eZ\Publish\Core\MVC\Symfony\View\ParametersInjector */
    private $viewParametersInjector;

    /** @var string */
    private $defaultTemplate;

    /** @var ActionDispatcherInterface */
    private $contentActionDispatcher;

    /** @var \eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface */
    private $languagePreferenceProvider;

    /**
     * @param \eZ\Publish\API\Repository\Repository $repository
     * @param \eZ\Publish\Core\MVC\Symfony\View\Configurator $viewConfigurator
     * @param \eZ\Publish\Core\MVC\Symfony\View\ParametersInjector $viewParametersInjector
     * @param $defaultTemplate
     * @param \EzSystems\EzPlatformAdminUi\Form\ActionDispatcher\ActionDispatcherInterface $contentActionDispatcher
     * @param \eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProviderInterface $languagePreferenceProvider
     */
    public function __construct(
        Repository $repository,
        Configurator $viewConfigurator,
        ParametersInjector $viewParametersInjector,
        $defaultTemplate,
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
        return 'EzSystems\EzPlatformAdminUiBundle\Controller\ContentEditController::createWithoutDraftAction' === $argument;
    }

    /**
     * @param array $parameters
     *
     * @return \EzSystems\EzPlatformAdminUi\View\ContentCreateSuccessView|\EzSystems\EzPlatformAdminUi\View\ContentCreateView
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function buildView(array $parameters)
    {
        // @todo improve default templates injection
        $view = new ContentCreateView($this->defaultTemplate);

        $language = $this->resolveLanguage($parameters);
        $location = $this->resolveLocation($parameters);
        $contentType = $this->resolveContentType($parameters, $this->languagePreferenceProvider->getPreferredLanguages());
        $form = $parameters['form'];

        if ($form->isSubmitted() && $form->isValid() && null !== $form->getClickedButton()) {
            $this->contentActionDispatcher->dispatchFormAction(
                $form,
                $form->getData(),
                $form->getClickedButton()->getName(),
                ['referrerLocation' => $location]
            );

            if ($response = $this->contentActionDispatcher->getResponse()) {
                $view = new ContentCreateSuccessView($response);
                $view->setLocation($location);

                return $view;
            }
        }

        $view->setContentType($contentType);
        $view->setLanguage($language);
        $view->setLocation($location);
        $view->setForm($form);

        $view->addParameters([
            'content_type' => $contentType,
            'language' => $language,
            'parent_location' => $location,
            'form' => $form->createView(),
        ]);

        $this->viewParametersInjector->injectViewParameters($view, $parameters);
        $this->viewConfigurator->configure($view);

        return $view;
    }

    /**
     * Loads a visible Location.
     *
     * @param int $locationId
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Location
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
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
     * Loads ContentType with identifier $contentTypeIdentifier.
     *
     * @param string $contentTypeIdentifier
     * @param string[] $prioritizedLanguages
     *
     * @return \eZ\Publish\API\Repository\Values\ContentType\ContentType
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    private function loadContentType(string $contentTypeIdentifier, array $prioritizedLanguages = []): ContentType
    {
        return $this->repository->getContentTypeService()->loadContentTypeByIdentifier(
            $contentTypeIdentifier,
            $prioritizedLanguages
        );
    }

    /**
     * @param array $parameters
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Language
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
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
     * @param array $languageCodes
     *
     * @return \eZ\Publish\API\Repository\Values\ContentType\ContentType
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    private function resolveContentType(array $parameters, array $languageCodes): ContentType
    {
        if (isset($parameters['contentType'])) {
            return $parameters['contentType'];
        }

        if (isset($parameters['contentTypeIdentifier'])) {
            return $this->loadContentType($parameters['contentTypeIdentifier'], $languageCodes);
        }

        throw new InvalidArgumentException(
            'ContentType',
            'No content type could be loaded from parameters'
        );
    }

    /**
     * @param array $parameters
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Location
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    private function resolveLocation(array $parameters): Location
    {
        if (isset($parameters['parentLocation'])) {
            return $parameters['parentLocation'];
        }

        if (isset($parameters['parentLocationId'])) {
            return $this->loadLocation((int) $parameters['parentLocationId']);
        }

        throw new InvalidArgumentException(
            'ParentLocation',
            'Unable to load parent location from parameters'
        );
    }
}
