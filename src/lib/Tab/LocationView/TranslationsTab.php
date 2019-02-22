<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab\LocationView;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\Location;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Translation\MainTranslationUpdateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Translation\TranslationAddData;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Translation\TranslationDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Type\Content\Translation\MainTranslationUpdateType;
use EzSystems\EzPlatformAdminUi\Form\Type\Content\Translation\TranslationAddType;
use EzSystems\EzPlatformAdminUi\Form\Type\Content\Translation\TranslationDeleteType;
use EzSystems\EzPlatformAdminUi\Tab\AbstractEventDispatchingTab;
use EzSystems\EzPlatformAdminUi\Tab\OrderedTabInterface;
use EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
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

    /** @var \Symfony\Component\Form\FormFactoryInterface */
    private $formFactory;

    /** @var UrlGeneratorInterface */
    protected $urlGenerator;

    /**
     * @param Environment $twig
     * @param TranslatorInterface $translator
     * @param DatasetFactory $datasetFactory
     * @param UrlGeneratorInterface $urlGenerator
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @param \Symfony\Component\Form\FormFactoryInterface $formFactory
     */
    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        DatasetFactory $datasetFactory,
        UrlGeneratorInterface $urlGenerator,
        EventDispatcherInterface $eventDispatcher,
        FormFactoryInterface $formFactory
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

        $mainTranslationUpdateForm = $this->createMainLanguageUpdateForm(
            $versionInfo->contentInfo,
            $versionInfo->contentInfo->mainLanguageCode
        );

        $viewParameters = [
            'translations' => $translationsDataset->getTranslations(),
            'form_translation_add' => $translationAddForm->createView(),
            'form_translation_remove' => $translationDeleteForm->createView(),
            'form_translation_remove' => $translationDeleteForm->createView(),
            'form_main_translation_update' => $mainTranslationUpdateForm->createView(),
            'main_translation_switch' => true,
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
        $data = new TranslationAddData($location);

        return $this->formFactory->createNamed('add-translation', TranslationAddType::class, $data);
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
        $data = new TranslationDeleteData(
            $location->getContentInfo(),
            array_combine($languageCodes, array_fill_keys($languageCodes, false))
        );

        return $this->formFactory->createNamed('delete-translations', TranslationDeleteType::class, $data);
    }

    /**
     * @param ContentInfo $contentInfo
     * @param string $languageCode
     *
     * @return FormInterface
     *
     * @throws InvalidOptionsException
     */
    private function createMainLanguageUpdateForm(ContentInfo $contentInfo, string $languageCode): FormInterface
    {
        $data = new MainTranslationUpdateData($contentInfo, $languageCode);

        return $this->formFactory->create(MainTranslationUpdateType::class, $data);
    }
}
