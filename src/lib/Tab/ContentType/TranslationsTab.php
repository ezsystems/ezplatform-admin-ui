<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab\ContentType;

use EzSystems\EzPlatformAdminUi\Form\Data\ContentType\Translation\TranslationAddData;
use EzSystems\EzPlatformAdminUi\Form\Data\ContentType\Translation\TranslationRemoveData;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Tab\AbstractTab;
use EzSystems\EzPlatformAdminUi\Tab\OrderedTabInterface;
use EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;

class TranslationsTab extends AbstractTab implements OrderedTabInterface
{
    const URI_FRAGMENT = 'ez-tab-content-type-view-translations';

    /** @var \EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory */
    protected $datasetFactory;

    /** @var \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory */
    protected $formFactory;

    /**
     * @param \Twig\Environment $twig
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     * @param \EzSystems\EzPlatformAdminUi\UI\Dataset\DatasetFactory $datasetFactory
     * @param \EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory $formFactory
     */
    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        DatasetFactory $datasetFactory,
        FormFactory $formFactory
    ) {
        parent::__construct($twig, $translator);

        $this->datasetFactory = $datasetFactory;
        $this->formFactory = $formFactory;
    }

    public function getIdentifier(): string
    {
        return 'translations';
    }

    public function getName(): string
    {
        /** @Desc("Translations") */
        return $this->translator->trans('tab.name.translations', [], 'content_type');
    }

    public function getOrder(): int
    {
        return 200;
    }

    public function renderView(array $parameters): string
    {
        /** @var \eZ\Publish\API\Repository\Values\ContentType\ContentType $contentType */
        $contentType = $parameters['content_type'];
        $contentTypeGroup = $parameters['content_type_group'];

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
            'can_translate' => $parameters['can_update'],
            'translations' => $translationsDataset->getTranslations(),
            'form_translation_add' => $translationAddForm->createView(),
            'form_translation_remove' => $translationRemoveForm->createView(),
        ];

        return $this->twig->render(
            '@ezdesign/admin/content_type/tab/translations.html.twig',
            array_merge($viewParameters, $parameters)
        );
    }
}
