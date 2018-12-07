<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab\LocationView;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Translation\TranslationAddData;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Translation\TranslationDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Tab\AbstractEventDispatchingTab;
use EzSystems\EzPlatformAdminUi\Tab\OrderedTabInterface;
use EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;

class TranslationsTab extends AbstractEventDispatchingTab implements OrderedTabInterface
{
    const URI_FRAGMENT = 'ez-tab-location-view-translations';

    /** @var DatasetFactory */
    protected $datasetFactory;

    /** @var FormFactory */
    protected $formFactory;

    /** @var UrlGeneratorInterface */
    protected $urlGenerator;

    /**
     * @param Environment $twig
     * @param TranslatorInterface $translator
     * @param DatasetFactory $datasetFactory
     * @param FormFactory $formFactory
     * @param UrlGeneratorInterface $urlGenerator
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        DatasetFactory $datasetFactory,
        FormFactory $formFactory,
        UrlGeneratorInterface $urlGenerator,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct($twig, $translator, $eventDispatcher);

        $this->datasetFactory = $datasetFactory;
        $this->formFactory = $formFactory;
        $this->urlGenerator = $urlGenerator;
    }

    public function getIdentifier(): string
    {
        return 'translations';
    }

    public function getName(): string
    {
        /** @Desc("Translations") */
        return $this->translator->trans('tab.name.translations', [], 'locationview');
    }

    public function getOrder(): int
    {
        return 600;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplate(): string
    {
        return '@ezdesign/content/tab/translations/tab.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateParameters(array $contextParameters = []): array
    {
        /** @var Location $location */
        $location = $contextParameters['location'];
        /** @var Content $content */
        $content = $contextParameters['content'];
        $versionInfo = $content->getVersionInfo();
        $translationsDataset = $this->datasetFactory->translations();
        $translationsDataset->load($versionInfo);

        $translationAddForm = $this->createTranslationAddForm($location);

        $translationDeleteForm = $this->createTranslationDeleteForm(
            $location,
            $translationsDataset->getLanguageCodes()
        );

        $viewParameters = [
            'translations' => $translationsDataset->getTranslations(),
            'form_translation_add' => $translationAddForm->createView(),
            'form_translation_remove' => $translationDeleteForm->createView(),
        ];

        return array_replace($contextParameters, $viewParameters);
    }

    /**
     * @param Location $location
     *
     * @return FormInterface
     *
     * @throws InvalidOptionsException
     */
    private function createTranslationAddForm(Location $location): FormInterface
    {
        $translationAddData = new TranslationAddData($location);

        return $this->formFactory->addTranslation($translationAddData);
    }

    /**
     * @param Location $location
     * @param array $languageCodes
     *
     * @return FormInterface
     *
     * @throws InvalidOptionsException
     */
    private function createTranslationDeleteForm(Location $location, array $languageCodes): FormInterface
    {
        $translationDeleteData = new TranslationDeleteData(
            $location->getContentInfo(),
            array_combine($languageCodes, array_fill_keys($languageCodes, false))
        );

        return $this->formFactory->deleteTranslation($translationDeleteData);
    }
}
