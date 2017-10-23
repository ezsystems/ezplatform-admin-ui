<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Factory;

use EzSystems\EzPlatformAdminUi\Form\Data\Content\Location\ContentLocationAddData;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Location\ContentLocationRemoveData;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Translation\TranslationRemoveData;
use EzSystems\EzPlatformAdminUi\Form\Data\Language\LanguageCreateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Language\LanguageDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\Language\LanguageUpdateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationCopyData;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationMoveData;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationSwapData;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationTrashData;
use EzSystems\EzPlatformAdminUi\Form\Data\OnFailureRedirect;
use EzSystems\EzPlatformAdminUi\Form\Data\OnSuccessRedirect;
use EzSystems\EzPlatformAdminUi\Form\Data\Section\SectionContentAssignData;
use EzSystems\EzPlatformAdminUi\Form\Data\Section\SectionCreateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Section\SectionDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\Section\SectionUpdateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Trash\TrashEmptyData;
use EzSystems\EzPlatformAdminUi\Form\Data\Trash\TrashItemRestoreData;
use EzSystems\EzPlatformAdminUi\Form\Data\Version\VersionRemoveData;
use EzSystems\EzPlatformAdminUi\Form\Type\Content\Location\ContentLocationAddType;
use EzSystems\EzPlatformAdminUi\Form\Type\Content\Location\ContentLocationRemoveType;
use EzSystems\EzPlatformAdminUi\Form\Type\Content\Translation\TranslationRemoveType;
use EzSystems\EzPlatformAdminUi\Form\Type\Language\LanguageCreateType;
use EzSystems\EzPlatformAdminUi\Form\Type\Language\LanguageDeleteType;
use EzSystems\EzPlatformAdminUi\Form\Type\Language\LanguageUpdateType;
use EzSystems\EzPlatformAdminUi\Form\Type\Location\LocationCopyType;
use EzSystems\EzPlatformAdminUi\Form\Type\Location\LocationMoveType;
use EzSystems\EzPlatformAdminUi\Form\Type\Location\LocationSwapType;
use EzSystems\EzPlatformAdminUi\Form\Type\Location\LocationTrashType;
use EzSystems\EzPlatformAdminUi\Form\Type\Section\SectionContentAssignType;
use EzSystems\EzPlatformAdminUi\Form\Type\Section\SectionCreateType;
use EzSystems\EzPlatformAdminUi\Form\Type\Section\SectionDeleteType;
use EzSystems\EzPlatformAdminUi\Form\Type\Section\SectionUpdateType;
use EzSystems\EzPlatformAdminUi\Form\Type\Trash\TrashEmptyType;
use EzSystems\EzPlatformAdminUi\Form\Type\Trash\TrashItemRestoreType;
use EzSystems\EzPlatformAdminUi\Form\Type\Version\VersionRemoveType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Util\StringUtil;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class FormFactory
{
    /** @var FormFactoryInterface */
    protected $formFactory;

    /** @var UrlGeneratorInterface */
    protected $urlGenerator;

    /**
     * @param FormFactoryInterface $formFactory
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(FormFactoryInterface $formFactory, UrlGeneratorInterface $urlGenerator)
    {
        $this->formFactory = $formFactory;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param TranslationRemoveData|null $data
     * @param null|string $successRedirectionUrl
     * @param null|string $failureRedirectionUrl
     * @param null|string $name
     *
     * @return FormInterface
     */
    public function removeTranslation(
        TranslationRemoveData $data = null,
        ?string $successRedirectionUrl = null,
        ?string $failureRedirectionUrl = null,
        ?string $name = null
    ): FormInterface {
        /** @var TranslationRemoveData $data */
        $data = $this->prepareRedirectableData(
            $data,
            $successRedirectionUrl,
            $failureRedirectionUrl
        );

        return $this->formFactory->createNamed($name, TranslationRemoveType::class, $data);
    }

    /**
     * @param VersionRemoveData|null $data
     * @param null|string $successRedirectionUrl
     * @param null|string $failureRedirectionUrl
     * @param null|string $name
     *
     * @return FormInterface
     */
    public function removeVersion(
        VersionRemoveData $data = null,
        ?string $successRedirectionUrl = null,
        ?string $failureRedirectionUrl = null,
        ?string $name = null
    ): FormInterface {
        /** @var VersionRemoveData $data */
        $data = $this->prepareRedirectableData(
            $data,
            $successRedirectionUrl,
            $failureRedirectionUrl
        );
        $name = $name ?: StringUtil::fqcnToBlockPrefix(VersionRemoveType::class);

        return $this->formFactory->createNamed($name, VersionRemoveType::class, $data);
    }

    /**
     * @param ContentLocationAddData|null $data
     * @param null|string $successRedirectionUrl
     * @param null|string $failureRedirectionUrl
     * @param null|string $name
     *
     * @return FormInterface
     */
    public function addLocation(
        ContentLocationAddData $data = null,
        ?string $successRedirectionUrl = null,
        ?string $failureRedirectionUrl = null,
        ?string $name = null
    ): FormInterface {
        /** @var ContentLocationAddData $data */
        $data = $this->prepareRedirectableData(
            $data,
            $successRedirectionUrl,
            $failureRedirectionUrl
        );
        $name = $name ?: StringUtil::fqcnToBlockPrefix(ContentLocationAddType::class);

        return $this->formFactory->createNamed($name, ContentLocationAddType::class, $data);
    }

    /**
     * @param ContentLocationRemoveData|null $data
     * @param null|string $successRedirectionUrl
     * @param null|string $failureRedirectionUrl
     * @param null|string $name
     *
     * @return FormInterface
     */
    public function removeLocation(
        ContentLocationRemoveData $data = null,
        ?string $successRedirectionUrl = null,
        ?string $failureRedirectionUrl = null,
        ?string $name = null
    ): FormInterface {
        /** @var ContentLocationRemoveData $data */
        $data = $this->prepareRedirectableData(
            $data,
            $successRedirectionUrl,
            $failureRedirectionUrl
        );
        $name = $name ?: StringUtil::fqcnToBlockPrefix(ContentLocationRemoveType::class);

        return $this->formFactory->createNamed($name, ContentLocationRemoveType::class, $data);
    }

    /**
     * @param LocationSwapData|null $data
     * @param null|string $successRedirectionUrl
     * @param null|string $failureRedirectionUrl
     * @param null|string $name
     *
     * @return FormInterface
     */
    public function swapLocation(
        LocationSwapData $data = null,
        ?string $successRedirectionUrl = null,
        ?string $failureRedirectionUrl = null,
        ?string $name = null
    ): FormInterface {
        /** @var LocationSwapData $data */
        $data = $this->prepareRedirectableData(
            $data,
            $successRedirectionUrl,
            $failureRedirectionUrl
        );
        $name = $name ?: StringUtil::fqcnToBlockPrefix(LocationSwapType::class);

        return $this->formFactory->createNamed($name, LocationSwapType::class, $data);
    }

    /**
     * @param LocationTrashData|null $data
     * @param null|string $successRedirectionUrl
     * @param null|string $failureRedirectionUrl
     * @param null|string $name
     *
     * @return FormInterface
     */
    public function trashLocation(
        LocationTrashData $data = null,
        ?string $successRedirectionUrl = null,
        ?string $failureRedirectionUrl = null,
        ?string $name = null
    ): FormInterface {
        $data = $this->prepareRedirectableData(
            $data,
            $successRedirectionUrl,
            $failureRedirectionUrl
        );
        $name = $name ?: StringUtil::fqcnToBlockPrefix(LocationTrashType::class);

        return $this->formFactory->createNamed($name, LocationTrashType::class, $data);
    }

    /**
     * @param LocationMoveData|null $data
     * @param null|string $successRedirectionUrl
     * @param null|string $failureRedirectionUrl
     * @param null|string $name
     *
     * @return FormInterface
     */
    public function moveLocation(
        LocationMoveData $data = null,
        ?string $successRedirectionUrl = null,
        ?string $failureRedirectionUrl = null,
        ?string $name = null
    ): FormInterface {
        $data = $this->prepareRedirectableData(
            $data,
            $successRedirectionUrl,
            $failureRedirectionUrl
        );
        $name = $name ?: StringUtil::fqcnToBlockPrefix(LocationMoveType::class);

        return $this->formFactory->createNamed($name, LocationMoveType::class, $data);
    }

    /**
     * @param LocationCopyData|null $data
     * @param null|string $successRedirectionUrl
     * @param null|string $failureRedirectionUrl
     * @param null|string $name
     *
     * @return FormInterface
     */
    public function copyLocation(
        LocationCopyData $data = null,
        ?string $successRedirectionUrl = null,
        ?string $failureRedirectionUrl = null,
        ?string $name = null
    ): FormInterface {
        $data = $this->prepareRedirectableData(
            $data,
            $successRedirectionUrl,
            $failureRedirectionUrl
        );
        $name = $name ?: StringUtil::fqcnToBlockPrefix(LocationCopyType::class);

        return $this->formFactory->createNamed($name, LocationCopyType::class, $data);
    }

    /**
     * @param TrashItemRestoreData|null $data
     * @param string|null $successRedirectionUrl
     * @param string|null $failureRedirectionUrl
     * @param null|string $name
     *
     * @return FormInterface
     */
    public function restoreTrashItem(
        TrashItemRestoreData $data = null,
        ?string $successRedirectionUrl = null,
        ?string $failureRedirectionUrl = null,
        ?string $name = null
    ): FormInterface {
        /** @var TrashItemRestoreData $data */
        $data = $this->prepareRedirectableData(
            $data,
            $successRedirectionUrl,
            $failureRedirectionUrl
        );
        $name = $name ?: StringUtil::fqcnToBlockPrefix(TrashItemRestoreType::class);

        return $this->formFactory->createNamed($name, TrashItemRestoreType::class, $data);
    }

    /**
     * @param TrashEmptyData|null $data
     * @param string|null $successRedirectionUrl
     * @param string|null $failureRedirectionUrl
     * @param null|string $name
     *
     * @return FormInterface
     */
    public function emptyTrash(
        TrashEmptyData $data = null,
        ?string $successRedirectionUrl = null,
        ?string $failureRedirectionUrl = null,
        ?string $name = null
    ): FormInterface {
        /** @var TrashEmptyData $data */
        $data = $this->prepareRedirectableData(
            $data,
            $successRedirectionUrl,
            $failureRedirectionUrl
        );
        $name = $name ?: StringUtil::fqcnToBlockPrefix(TrashEmptyType::class);

        return $this->formFactory->createNamed($name, TrashEmptyType::class, $data);
    }

    /**
     * @param SectionContentAssignData|null $data
     * @param string|null $successRedirectionUrl
     * @param string|null $failureRedirectionUrl
     * @param null|string $name
     *
     * @return FormInterface
     */
    public function assignContentSectionForm(
        SectionContentAssignData $data = null,
        ?string $successRedirectionUrl = null,
        ?string $failureRedirectionUrl = null,
        ?string $name = null
    ): FormInterface {
        /** @var SectionContentAssignData $data */
        $data = $this->prepareRedirectableData(
            $data,
            $successRedirectionUrl,
            $failureRedirectionUrl
        );
        $name = $name ?: sprintf('content-assign-section-%s', $data->getSection()->id);

        return $this->formFactory->createNamed($name, SectionContentAssignType::class, $data);
    }

    /**
     * @param SectionDeleteData|null $data
     * @param null|string $successRedirectionUrl
     * @param null|string $failureRedirectionUrl
     * @param null|string $name
     *
     * @return FormInterface
     */
    public function deleteSection(
        SectionDeleteData $data = null,
        ?string $successRedirectionUrl = null,
        ?string $failureRedirectionUrl = null,
        ?string $name = null
    ): FormInterface {
        /** @var SectionDeleteData $data */
        $data = $this->prepareRedirectableData(
            $data,
            $successRedirectionUrl,
            $failureRedirectionUrl
        );
        $name = $name ?: sprintf('delete-section-%d', $data->getSection()->id);

        return $this->formFactory->createNamed($name, SectionDeleteType::class, $data);
    }

    /**
     * @param SectionCreateData|null $data
     * @param null|string $successRedirectionUrl
     * @param null|string $failureRedirectionUrl
     * @param null|string $name
     *
     * @return FormInterface
     */
    public function createSection(
        ?SectionCreateData $data = null,
        ?string $successRedirectionUrl = null,
        ?string $failureRedirectionUrl = null,
        ?string $name = null
    ): FormInterface {
        /** @var SectionCreateData $data */
        $data = $this->prepareRedirectableData(
            $data ?? new SectionCreateData(),
            $successRedirectionUrl,
            $failureRedirectionUrl
        );
        $name = $name ?: StringUtil::fqcnToBlockPrefix(SectionCreateType::class);

        return $this->formFactory->createNamed($name, SectionCreateType::class, $data);
    }

    /**
     * @param SectionUpdateData|null $data
     * @param string|null $successRedirectionUrl
     * @param string|null $failureRedirectionUrl
     * @param null|string $name
     *
     * @return FormInterface
     */
    public function updateSection(
        SectionUpdateData $data = null,
        ?string $successRedirectionUrl = null,
        ?string $failureRedirectionUrl = null,
        ?string $name = null
    ): FormInterface {
        /** @var SectionUpdateData $data */
        $data = $this->prepareRedirectableData(
            $data,
            $successRedirectionUrl,
            $failureRedirectionUrl
        );
        $name = $name ?: sprintf('update-section-%s', $data->getSection()->id);

        return $this->formFactory->createNamed($name, SectionUpdateType::class, $data);
    }

    /**
     * @param LanguageCreateData|null $data
     * @param null|string $successRedirectionUrl
     * @param null|string $failureRedirectionUrl
     * @param null|string $name
     *
     * @return FormInterface
     */
    public function createLanguage(
        ?LanguageCreateData $data = null,
        ?string $successRedirectionUrl = null,
        ?string $failureRedirectionUrl = null,
        ?string $name = null
    ): FormInterface {
        $data = $this->prepareRedirectableData(
            $data ?? new LanguageCreateData(),
            $successRedirectionUrl,
            $failureRedirectionUrl
        );
        $name = $name ?: StringUtil::fqcnToBlockPrefix(LanguageCreateType::class);

        return $this->formFactory->createNamed($name, LanguageCreateType::class, $data);
    }

    /**
     * @param LanguageUpdateData $data
     * @param null|string $successRedirectionUrl
     * @param null|string $failureRedirectionUrl
     * @param null|string $name
     *
     * @return FormInterface
     */
    public function updateLanguage(
        LanguageUpdateData $data,
        ?string $successRedirectionUrl = null,
        ?string $failureRedirectionUrl = null,
        ?string $name = null
    ): FormInterface {
        /** @var LanguageUpdateData $data */
        $data = $this->prepareRedirectableData(
            $data,
            $successRedirectionUrl,
            $failureRedirectionUrl
        );
        $name = $name ?: sprintf('update-language-%d', $data->getLanguage()->id);

        return $this->formFactory->createNamed($name, LanguageUpdateType::class, $data);
    }

    /**
     * @param LanguageDeleteData $data
     * @param null|string $successRedirectionUrl
     * @param null|string $failureRedirectionUrl
     * @param null|string $name
     *
     * @return FormInterface
     */
    public function deleteLanguage(
        LanguageDeleteData $data,
        ?string $successRedirectionUrl = null,
        ?string $failureRedirectionUrl = null,
        ?string $name = null
    ): FormInterface {
        /** @var LanguageDeleteData $data */
        $data = $this->prepareRedirectableData(
            $data,
            $successRedirectionUrl,
            $failureRedirectionUrl
        );
        $name = $name ?: sprintf('delete-language-%d', $data->getLanguage()->id);

        return $this->formFactory->createNamed($name, LanguageDeleteType::class, $data);
    }

    /**
     * Fill redirection fields if fitting interface is implemented.
     *
     * @param mixed $data
     * @param null|string $successRedirectionUrl
     * @param null|string $failureRedirectionUrl
     *
     * @return mixed
     */
    protected function prepareRedirectableData(
        $data,
        ?string $successRedirectionUrl = null,
        ?string $failureRedirectionUrl = null
    ) {
        if ($data instanceof OnSuccessRedirect) {
            $data->setOnSuccessRedirectionUrl($successRedirectionUrl);
        }

        if ($data instanceof OnFailureRedirect) {
            $data->setOnFailureRedirectionUrl($failureRedirectionUrl);
        }

        return $data;
    }
}
