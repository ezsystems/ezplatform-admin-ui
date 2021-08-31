<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab\ContentType;

use EzSystems\EzPlatformAdminUi\Form\Data\ContentType\Translation\TranslationAddData;
use EzSystems\EzPlatformAdminUi\Form\Data\ContentType\Translation\TranslationRemoveData;
use EzSystems\EzPlatformAdminUi\Form\Factory\ContentTypeFormFactory;
use EzSystems\EzPlatformAdminUi\Tab\AbstractEventDispatchingTab;
use EzSystems\EzPlatformAdminUi\Tab\OrderedTabInterface;
use EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class TranslationsTab extends AbstractEventDispatchingTab implements OrderedTabInterface
{
    const URI_FRAGMENT = 'ibexa-tab-content-type-view-translations';

    /** @var \EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory */
    protected $datasetFactory;

    /** @var \EzSystems\EzPlatformAdminUi\Form\Factory\ContentTypeFormFactory */
    protected $formFactory;

    /**
     * @param \Twig\Environment $twig
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @param \EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory $datasetFactory
     * @param \EzSystems\EzPlatformAdminUi\Form\Factory\ContentTypeFormFactory $formFactory
     */
    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        EventDispatcherInterface $eventDispatcher,
        DatasetFactory $datasetFactory,
        ContentTypeFormFactory $formFactory
    ) {
        parent::__construct($twig, $translator, $eventDispatcher);

        $this->datasetFactory = $datasetFactory;
        $this->formFactory = $formFactory;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'translations';
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        /** @Desc("Translations") */
        return $this->translator->trans('tab.name.translations', [], 'content_type');
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return 200;
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return '@ezdesign/content_type/tab/translations.html.twig';
    }

    /**
     * @param mixed[] $contextParameters
     *
     * @return mixed[]
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function getTemplateParameters(array $contextParameters = []): array
    {
        /** @var \eZ\Publish\API\Repository\Values\ContentType\ContentType $contentType */
        $contentType = $contextParameters['content_type'];
        $contentTypeGroup = $contextParameters['content_type_group'];

        $translationsDataset = $this->datasetFactory->translations();
        $translationsDataset->loadFromContentType($contentType);

        $translationAddForm = $this->formFactory->addContentTypeTranslation(
            new TranslationAddData(
                $contentType,
                $contentTypeGroup
            )
        );

        $translationRemoveForm = $this->formFactory->removeContentTypeTranslation(
            new TranslationRemoveData(
                $contentType,
                $contentTypeGroup,
                array_fill_keys($translationsDataset->getLanguageCodes(), false)
            )
        );

        $viewParameters = [
            'can_translate' => $contextParameters['can_update'],
            'translations' => $translationsDataset->getTranslations(),
            'form_translation_add' => $translationAddForm->createView(),
            'form_translation_remove' => $translationRemoveForm->createView(),
            'main_translation_switch' => false,
        ];

        return array_replace($contextParameters, $viewParameters);
    }
}
