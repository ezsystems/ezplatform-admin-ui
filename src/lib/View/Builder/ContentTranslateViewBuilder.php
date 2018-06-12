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
use eZ\Publish\Core\MVC\Symfony\View\Builder\ViewBuilder;
use eZ\Publish\Core\MVC\Symfony\View\Configurator;
use eZ\Publish\Core\MVC\Symfony\View\ParametersInjector;
use EzSystems\EzPlatformAdminUi\View\ContentTranslateSuccessView;
use EzSystems\EzPlatformAdminUi\View\ContentTranslateView;
use EzSystems\RepositoryForms\Form\ActionDispatcher\ActionDispatcherInterface;

/**
 * Builds ContentEditView objects.
 *
 * @internal
 */
class ContentTranslateViewBuilder implements ViewBuilder
{
    /** @var \eZ\Publish\API\Repository\Repository */
    private $repository;

    /** @var \eZ\Publish\Core\MVC\Symfony\View\Configurator */
    private $viewConfigurator;

    /** @var \eZ\Publish\Core\MVC\Symfony\View\ParametersInjector */
    private $viewParametersInjector;

    /** @var \EzSystems\RepositoryForms\Form\ActionDispatcher\ActionDispatcherInterface */
    private $contentActionDispatcher;

    public function __construct(
        Repository $repository,
        Configurator $viewConfigurator,
        ParametersInjector $viewParametersInjector,
        ActionDispatcherInterface $contentActionDispatcher
    ) {
        $this->repository = $repository;
        $this->viewConfigurator = $viewConfigurator;
        $this->viewParametersInjector = $viewParametersInjector;
        $this->contentActionDispatcher = $contentActionDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function matches($argument)
    {
        return 'EzSystems\EzPlatformAdminUiBundle\Controller\ContentEditController::translateAction' === $argument;
    }

    /**
     * @param array $parameters
     *
     * @return \EzSystems\EzPlatformAdminUi\View\ContentTranslateSuccessView|\EzSystems\EzPlatformAdminUi\View\ContentTranslateView
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentType
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function buildView(array $parameters)
    {
        $view = new ContentTranslateView();

        $fromLanguage = $this->resolveFromLanguage($parameters);
        $toLanguage = $this->resolveToLanguage($parameters);
        $location = $this->resolveLocation($parameters);
        $content = $this->resolveContent($parameters, $location, $fromLanguage);
        $contentInfo = $content->contentInfo;
        $contentType = $this->loadContentType($contentInfo->contentTypeId);
        /** @var \Symfony\Component\Form\FormInterface $form */
        $form = $parameters['form'];

        if (null === $location && $contentInfo->isPublished()) {
            // assume main location if no location was provided
            $location = $this->loadLocation((int) $contentInfo->mainLocationId);
        }

        if ($form->isValid() && null !== $form->getClickedButton()) {
            $this->contentActionDispatcher->dispatchFormAction(
                $form,
                $form->getData(),
                $form->getClickedButton()->getName(),
                ['referrerLocation' => $location]
            );

            if ($response = $this->contentActionDispatcher->getResponse()) {
                return new ContentTranslateSuccessView($response);
            }
        }

        $formView = $form->createView();

        $view->setContent($content);
        $view->setContentType($contentType);
        $view->setLanguage($toLanguage);
        $view->setBaseLanguage($fromLanguage);
        $view->setLocation($location);
        $view->setForm($parameters['form']);
        $view->setFormView($formView);

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
     * Loads ContentType with id $contentTypeId.
     *
     * @param int $contentTypeId
     *
     * @return \eZ\Publish\API\Repository\Values\ContentType\ContentType
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    private function loadContentType(int $contentTypeId): ContentType
    {
        return $this->repository->getContentTypeService()->loadContentType($contentTypeId);
    }

    /**
     * @param array $parameters
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Language|null
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    private function resolveFromLanguage(array $parameters): ?Language
    {
        if (isset($parameters['fromLanguage'])) {
            return $parameters['fromLanguage'];
        }

        if (isset($parameters['fromLanguageCode'])) {
            return $this->loadLanguage($parameters['fromLanguageCode']);
        }

        return null;
    }

    /**
     * @param array $parameters
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Language
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    private function resolveToLanguage(array $parameters): Language
    {
        if (isset($parameters['toLanguage'])) {
            return $parameters['toLanguage'];
        }

        if (isset($parameters['toLanguageCode'])) {
            return $this->loadLanguage($parameters['toLanguageCode']);
        }

        throw new InvalidArgumentException(
            'Language',
            'No language information provided. Are you missing \'toLanguage\' or \'toLanguageCode\' parameters'
        );
    }

    /**
     * @param array $parameters
     * @param \eZ\Publish\API\Repository\Values\Content\Location|null $location
     * @param \eZ\Publish\API\Repository\Values\Content\Language|null $language
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Content
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    private function resolveContent(array $parameters, ?Location $location, ?Language $language): Content
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
            null !== $language ? [$language->languageCode] : []
        );
    }

    /**
     * @param array $parameters
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Location|null
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    private function resolveLocation(array $parameters): ?Location
    {
        if (isset($parameters['location'])) {
            return $parameters['location'];
        }

        if (isset($parameters['locationId'])) {
            return $this->loadLocation((int) $parameters['locationId']);
        }

        return null;
    }
}
