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
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationCopyData;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationMoveData;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationSwapData;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationTrashData;
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
     * @param string|null $name
     * @param TranslationRemoveData|null $data
     * @param string|null $successRedirectionUrl
     * @param string|null $failureRedirectionUrl
     *
     * @return FormInterface
     */
    public function removeTranslation(
        ?string $name = null,
        TranslationRemoveData $data = null,
        string $successRedirectionUrl = null,
        string $failureRedirectionUrl = null
    ): FormInterface {
        $data = $data ?: new TranslationRemoveData();
        $uiFormData = new UiFormData($data, $successRedirectionUrl, $failureRedirectionUrl);

        return $this->createUiForm(
            $name, TranslationRemoveType::class, $uiFormData,
            $this->urlGenerator->generate('ezplatform.translation.remove'), []
        );
    }

    /**
     * @param string|null $name
     * @param VersionRemoveData|null $data
     * @param string|null $successRedirectionUrl
     * @param string|null $failureRedirectionUrl
     *
     * @return FormInterface
     */
    public function removeVersion(
        ?string $name = null,
        VersionRemoveData $data = null,
        string $successRedirectionUrl = null,
        string $failureRedirectionUrl = null
    ): FormInterface {
        $data = $data ?: new VersionRemoveData();
        $uiFormData = new UiFormData($data, $successRedirectionUrl, $failureRedirectionUrl);

        return $this->createUiForm(
            $name, VersionRemoveType::class, $uiFormData, $this->urlGenerator->generate('ezplatform.version.remove'), []
        );
    }

    /**
     * @param string|null $name
     * @param ContentLocationAddData|null $data
     * @param string|null $successRedirectionUrl
     * @param string|null $failureRedirectionUrl
     *
     * @return FormInterface
     */
    public function addLocation(
        ?string $name = null,
        ContentLocationAddData $data = null,
        string $successRedirectionUrl = null,
        string $failureRedirectionUrl = null
    ): FormInterface {
        $data = $data ?: new ContentLocationAddData();
        $name = $name ?: StringUtil::fqcnToBlockPrefix(ContentLocationAddType::class);
        $uiFormData = new UiFormData($data, $successRedirectionUrl, $failureRedirectionUrl);

        return $this->createUiForm(
            $name,
            ContentLocationAddType::class,
            $uiFormData,
            $this->urlGenerator->generate('ezplatform.location.add'),
            []
        );
    }

    /**
     * @param string|null $name
     * @param ContentLocationRemoveData|null $data
     * @param string|null $successRedirectionUrl
     * @param string|null $failureRedirectionUrl
     *
     * @return FormInterface
     */
    public function removeLocation(
        ?string $name = null,
        ContentLocationRemoveData $data = null,
        string $successRedirectionUrl = null,
        string $failureRedirectionUrl = null
    ): FormInterface {
        $data = $data ?: new ContentLocationRemoveData();
        $name = $name ?: StringUtil::fqcnToBlockPrefix(ContentLocationRemoveType::class);
        $uiFormData = new UiFormData($data, $successRedirectionUrl, $failureRedirectionUrl);

        return $this->createUiForm(
            $name,
            ContentLocationRemoveType::class,
            $uiFormData,
            $this->urlGenerator->generate('ezplatform.location.remove'),
            []
        );
    }

    /**
     * @param string|null $name
     * @param LocationSwapData|null $data
     * @param string|null $successRedirectionUrl
     * @param string|null $failureRedirectionUrl
     *
     * @return FormInterface
     */
    public function swapLocation(
        ?string $name = null,
        LocationSwapData $data = null,
        string $successRedirectionUrl = null,
        string $failureRedirectionUrl = null
    ): FormInterface {
        $data = $data ?: new LocationSwapData();
        $name = $name ?: StringUtil::fqcnToBlockPrefix(LocationSwapType::class);
        $uiFormData = new UiFormData($data, $successRedirectionUrl, $failureRedirectionUrl);

        return $this->createUiForm(
            $name,
            LocationSwapType::class,
            $uiFormData,
            $this->urlGenerator->generate('ezplatform.location.swap'),
            []
        );
    }

    /**
     * @param string|null $name
     * @param LocationTrashData|null $data
     * @param string|null $successRedirectionUrl
     * @param string|null $failureRedirectionUrl
     *
     * @return FormInterface
     */
    public function trashLocation(
        ?string $name = null,
        LocationTrashData $data = null,
        string $successRedirectionUrl = null,
        string $failureRedirectionUrl = null
    ): FormInterface {
        $data = $data ?: new LocationTrashData();
        $name = $name ?: StringUtil::fqcnToBlockPrefix(LocationTrashType::class);
        $uiFormData = new UiFormData($data, $successRedirectionUrl, $failureRedirectionUrl);

        return $this->createUiForm(
            $name,
            LocationTrashType::class,
            $uiFormData,
            $this->urlGenerator->generate('ezplatform.location.trash'),
            []
        );
    }

    /**
     * @param string|null $name
     * @param LocationMoveData|null $data
     * @param string|null $successRedirectionUrl
     * @param string|null $failureRedirectionUrl
     *
     * @return FormInterface
     */
    public function moveLocation(
        ?string $name = null,
        LocationMoveData $data = null,
        string $successRedirectionUrl = null,
        string $failureRedirectionUrl = null
    ): FormInterface {
        $data = $data ?: new LocationMoveData();
        $name = $name ?: StringUtil::fqcnToBlockPrefix(LocationMoveType::class);
        $uiFormData = new UiFormData($data, $successRedirectionUrl, $failureRedirectionUrl);

        return $this->createUiForm(
            $name,
            LocationMoveType::class,
            $uiFormData,
            $this->urlGenerator->generate('ezplatform.location.move'),
            []
        );
    }

    /**
     * @param string|null $name
     * @param LocationCopyData|null $data
     * @param string|null $successRedirectionUrl
     * @param string|null $failureRedirectionUrl
     *
     * @return FormInterface
     */
    public function copyLocation(
        ?string $name = null,
        LocationCopyData $data = null,
        string $successRedirectionUrl = null,
        string $failureRedirectionUrl = null
    ): FormInterface {
        $data = $data ?: new LocationCopyData();
        $name = $name ?: StringUtil::fqcnToBlockPrefix(LocationCopyType::class);
        $uiFormData = new UiFormData($data, $successRedirectionUrl, $failureRedirectionUrl);

        return $this->createUiForm(
            $name,
            LocationCopyType::class,
            $uiFormData,
            $this->urlGenerator->generate('ezplatform.location.copy'),
            []
        );
    }

    /**
     * @param string|null $name
     * @param TrashItemRestoreData|null $data
     * @param string|null $successRedirectionUrl
     * @param string|null $failureRedirectionUrl
     *
     * @return FormInterface
     */
    public function restoreTrashItem(
        ?string $name = null,
        TrashItemRestoreData $data = null,
        string $successRedirectionUrl = null,
        string $failureRedirectionUrl = null
    ): FormInterface {
        $data = $data ?: new TrashItemRestoreData();
        $name = $name ?: StringUtil::fqcnToBlockPrefix(TrashItemRestoreType::class);
        $uiFormData = new UiFormData($data, $successRedirectionUrl, $failureRedirectionUrl);

        return $this->createUiForm(
            $name,
            TrashItemRestoreType::class,
            $uiFormData,
            $this->urlGenerator->generate('ezplatform.trash.restore'),
            []
        );
    }

    /**
     * @param string|null $name
     * @param TrashEmptyData|null $data
     * @param string|null $successRedirectionUrl
     * @param string|null $failureRedirectionUrl
     *
     * @return FormInterface
     */
    public function emptyTrash(
        ?string $name = null,
        TrashEmptyData $data = null,
        string $successRedirectionUrl = null,
        string $failureRedirectionUrl = null
    ): FormInterface {
        $data = $data ?: new TrashEmptyData();
        $name = $name ?: StringUtil::fqcnToBlockPrefix(TrashEmptyType::class);
        $uiFormData = new UiFormData($data, $successRedirectionUrl, $failureRedirectionUrl);

        return $this->createUiForm(
            $name,
            TrashEmptyType::class,
            $uiFormData,
            $this->urlGenerator->generate('ezplatform.trash.empty'),
            []
        );
    }

    /**
     * @param int $sectionId
     * @param SectionContentAssignData|null $data
     * @param string|null $successRedirectionUrl
     * @param string|null $failureRedirectionUrl
     *
     * @return FormInterface
     */
    public function assignContentSectionForm(
        int $sectionId,
        SectionContentAssignData $data = null,
        string $successRedirectionUrl = null,
        string $failureRedirectionUrl = null
    ): FormInterface {
        $data = $data ?: new SectionContentAssignData();
        $name = sprintf('section_content_assign_%s', $sectionId);
        $uiFormData = new UiFormData($data, $successRedirectionUrl, $failureRedirectionUrl);

        return $this->createUiForm(
            $name,
            SectionContentAssignType::class,
            $uiFormData,
            $this->urlGenerator->generate('ezplatform.section.assign_content', ['sectionId' => $sectionId]),
            []
        );
    }

    /**
     * @param int $sectionId
     * @param SectionDeleteData|null $data
     * @param string|null $successRedirectionUrl
     * @param string|null $failureRedirectionUrl
     *
     * @return FormInterface
     */
    public function deleteSection(
        int $sectionId,
        SectionDeleteData $data = null,
        string $successRedirectionUrl = null,
        string $failureRedirectionUrl = null
    ): FormInterface {
        $data = $data ?: new SectionDeleteData();
        $name = sprintf('section_delete_%s', $sectionId);
        $uiFormData = new UiFormData($data, $successRedirectionUrl, $failureRedirectionUrl);

        return $this->createUiForm(
            $name,
            SectionDeleteType::class,
            $uiFormData,
            $this->urlGenerator->generate('ezplatform.section.delete', ['sectionId' => $sectionId]),
            []
        );
    }

    /**
     * @param string|null $name
     * @param SectionCreateData|null $data
     * @param string|null $successRedirectionUrl
     * @param string|null $failureRedirectionUrl
     *
     * @return FormInterface
     */
    public function createSection(
        ?string $name = null,
        SectionCreateData $data = null,
        string $successRedirectionUrl = null,
        string $failureRedirectionUrl = null
    ): FormInterface {
        $data = $data ?: new SectionCreateData();
        $name = $name ?: StringUtil::fqcnToBlockPrefix(SectionCreateType::class);
        $uiFormData = new UiFormData($data, $successRedirectionUrl, $failureRedirectionUrl);

        return $this->createUiForm(
            $name,
            SectionCreateType::class,
            $uiFormData,
            $this->urlGenerator->generate('ezplatform.section.create'),
            []
        );
    }

    /**
     * @param int $sectionId
     * @param SectionUpdateData|null $data
     * @param string|null $successRedirectionUrl
     * @param string|null $failureRedirectionUrl
     *
     * @return FormInterface
     */
    public function updateSection(
        int $sectionId,
        SectionUpdateData $data = null,
        string $successRedirectionUrl = null,
        string $failureRedirectionUrl = null
    ): FormInterface {
        $data = $data ?: new SectionUpdateData();
        $name = sprintf('section_update_%s', $sectionId);
        $uiFormData = new UiFormData($data, $successRedirectionUrl, $failureRedirectionUrl);

        return $this->createUiForm(
            $name,
            SectionUpdateType::class,
            $uiFormData,
            $this->urlGenerator->generate('ezplatform.section.update', ['sectionId' => $sectionId]),
            []
        );
    }

    /**
     * @param string|null $name
     * @param string $dataType
     * @param UiFormData $uiFormData
     * @param string $action
     * @param array $options
     *
     * @return FormInterface
     */
    public function createUiForm(
        ?string $name = null,
        string $dataType,
        UiFormData $uiFormData,
        string $action,
        array $options = []
    ): FormInterface {
        $defaultOptions = [
            'method' => Request::METHOD_POST,
            'action' => $action,
        ];
        $options = array_merge($defaultOptions, $options);

        if (null !== $name) {
            $formBuilder = $this->formFactory->createNamed($name, UiFormType::class, $uiFormData, $options);
        } else {
            $formBuilder = $this->formFactory->create(UiFormType::class, $uiFormData, $options);
        }

        $formBuilder->add('data', $dataType, ['label' => false]);

        return $formBuilder;
    }
}
