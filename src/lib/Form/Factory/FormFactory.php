<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Factory;

use EzSystems\EzPlatformAdminUi\Form\Data\Bookmark\BookmarkRemoveData;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\CustomUrl\CustomUrlAddData;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\CustomUrl\CustomUrlRemoveData;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Draft\ContentCreateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Draft\ContentEditData;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Draft\ContentRemoveData;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Location\ContentLocationAddData;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Location\ContentLocationRemoveData;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Location\ContentMainLocationUpdateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Translation\TranslationAddData;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Translation\TranslationDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\ContentType\ContentTypesDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\ContentTypeGroup\ContentTypeGroupCreateData;
use EzSystems\EzPlatformAdminUi\Form\Data\ContentTypeGroup\ContentTypeGroupDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\ContentTypeGroup\ContentTypeGroupsDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\ContentTypeGroup\ContentTypeGroupUpdateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Language\LanguageCreateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Language\LanguageDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\Language\LanguagesDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\Language\LanguageUpdateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationCopyData;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationCopySubtreeData;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationMoveData;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationSwapData;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationTrashData;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationUpdateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationUpdateVisibilityData;
use EzSystems\EzPlatformAdminUi\Form\Data\ObjectState\ObjectStateGroupCreateData;
use EzSystems\EzPlatformAdminUi\Form\Data\ObjectState\ObjectStateGroupDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\ObjectState\ObjectStateGroupsDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\ObjectState\ObjectStateGroupUpdateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Policy\PoliciesDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\Policy\PolicyCreateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Policy\PolicyDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\Policy\PolicyUpdateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Role\RoleAssignmentCreateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Role\RoleAssignmentDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\Role\RoleAssignmentsDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\Role\RoleCreateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Role\RoleDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\Role\RolesDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\Role\RoleUpdateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Search\SearchData;
use EzSystems\EzPlatformAdminUi\Form\Data\Section\SectionContentAssignData;
use EzSystems\EzPlatformAdminUi\Form\Data\Section\SectionCreateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Section\SectionDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\Section\SectionsDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\Section\SectionUpdateData;
use EzSystems\EzPlatformAdminUi\Form\Data\URL\URLListData;
use EzSystems\EzPlatformAdminUi\Form\Data\URL\URLUpdateData;
use EzSystems\EzPlatformAdminUi\Form\Data\URLWildcard\URLWildcardData;
use EzSystems\EzPlatformAdminUi\Form\Data\URLWildcard\URLWildcardDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\URLWildcard\URLWildcardUpdateData;
use EzSystems\EzPlatformAdminUi\Form\Data\User\UserDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\User\UserEditData;
use EzSystems\EzPlatformAdminUi\Form\Data\Version\VersionRemoveData;
use EzSystems\EzPlatformAdminUi\Form\Type\Bookmark\BookmarkRemoveType;
use EzSystems\EzPlatformAdminUi\Form\Type\Content\CustomUrl\CustomUrlAddType;
use EzSystems\EzPlatformAdminUi\Form\Type\Content\CustomUrl\CustomUrlRemoveType;
use EzSystems\EzPlatformAdminUi\Form\Type\Content\Draft\ContentCreateType;
use EzSystems\EzPlatformAdminUi\Form\Type\Content\Draft\ContentEditType;
use EzSystems\EzPlatformAdminUi\Form\Type\Content\Draft\ContentRemoveType;
use EzSystems\EzPlatformAdminUi\Form\Type\Content\Location\ContentLocationAddType;
use EzSystems\EzPlatformAdminUi\Form\Type\Content\Location\ContentLocationRemoveType;
use EzSystems\EzPlatformAdminUi\Form\Type\Content\Location\ContentMainLocationUpdateType;
use EzSystems\EzPlatformAdminUi\Form\Type\Content\Translation\TranslationAddType;
use EzSystems\EzPlatformAdminUi\Form\Type\Content\Translation\TranslationDeleteType;
use EzSystems\EzPlatformAdminUi\Form\Type\ContentType\ContentTypesDeleteType;
use EzSystems\EzPlatformAdminUi\Form\Type\ContentTypeGroup\ContentTypeGroupCreateType;
use EzSystems\EzPlatformAdminUi\Form\Type\ContentTypeGroup\ContentTypeGroupDeleteType;
use EzSystems\EzPlatformAdminUi\Form\Type\ContentTypeGroup\ContentTypeGroupsDeleteType;
use EzSystems\EzPlatformAdminUi\Form\Type\ContentTypeGroup\ContentTypeGroupUpdateType;
use EzSystems\EzPlatformAdminUi\Form\Type\Language\LanguageCreateType;
use EzSystems\EzPlatformAdminUi\Form\Type\Language\LanguageDeleteType;
use EzSystems\EzPlatformAdminUi\Form\Type\Language\LanguagesDeleteType;
use EzSystems\EzPlatformAdminUi\Form\Type\Language\LanguageUpdateType;
use EzSystems\EzPlatformAdminUi\Form\Type\Location\LocationCopySubtreeType;
use EzSystems\EzPlatformAdminUi\Form\Type\Location\LocationCopyType;
use EzSystems\EzPlatformAdminUi\Form\Type\Location\LocationMoveType;
use EzSystems\EzPlatformAdminUi\Form\Type\Location\LocationSwapType;
use EzSystems\EzPlatformAdminUi\Form\Type\Location\LocationTrashType;
use EzSystems\EzPlatformAdminUi\Form\Type\Location\LocationUpdateType;
use EzSystems\EzPlatformAdminUi\Form\Type\Location\LocationUpdateVisibilityType;
use EzSystems\EzPlatformAdminUi\Form\Type\ObjectState\ObjectStateGroupCreateType;
use EzSystems\EzPlatformAdminUi\Form\Type\ObjectState\ObjectStateGroupDeleteType;
use EzSystems\EzPlatformAdminUi\Form\Type\ObjectState\ObjectStateGroupsDeleteType;
use EzSystems\EzPlatformAdminUi\Form\Type\ObjectState\ObjectStateGroupUpdateType;
use EzSystems\EzPlatformAdminUi\Form\Type\Policy\PoliciesDeleteType;
use EzSystems\EzPlatformAdminUi\Form\Type\Policy\PolicyCreateType;
use EzSystems\EzPlatformAdminUi\Form\Type\Policy\PolicyCreateWithLimitationType;
use EzSystems\EzPlatformAdminUi\Form\Type\Policy\PolicyDeleteType;
use EzSystems\EzPlatformAdminUi\Form\Type\Policy\PolicyUpdateType;
use EzSystems\EzPlatformAdminUi\Form\Type\Role\RoleAssignmentCreateType;
use EzSystems\EzPlatformAdminUi\Form\Type\Role\RoleAssignmentDeleteType;
use EzSystems\EzPlatformAdminUi\Form\Type\Role\RoleAssignmentsDeleteType;
use EzSystems\EzPlatformAdminUi\Form\Type\Role\RoleCreateType;
use EzSystems\EzPlatformAdminUi\Form\Type\Role\RoleDeleteType;
use EzSystems\EzPlatformAdminUi\Form\Type\Role\RolesDeleteType;
use EzSystems\EzPlatformAdminUi\Form\Type\Role\RoleUpdateType;
use EzSystems\EzPlatformAdminUi\Form\Type\Search\SearchType;
use EzSystems\EzPlatformAdminUi\Form\Type\Section\SectionContentAssignType;
use EzSystems\EzPlatformAdminUi\Form\Type\Section\SectionCreateType;
use EzSystems\EzPlatformAdminUi\Form\Type\Section\SectionDeleteType;
use EzSystems\EzPlatformAdminUi\Form\Type\Section\SectionsDeleteType;
use EzSystems\EzPlatformAdminUi\Form\Type\Section\SectionUpdateType;
use EzSystems\EzPlatformAdminUi\Form\Type\URL\URLEditType;
use EzSystems\EzPlatformAdminUi\Form\Type\URL\URLListType;
use EzSystems\EzPlatformAdminUi\Form\Type\URLWildcard\URLWildcardDeleteType;
use EzSystems\EzPlatformAdminUi\Form\Type\URLWildcard\URLWildcardType;
use EzSystems\EzPlatformAdminUi\Form\Type\URLWildcard\URLWildcardUpdateType;
use EzSystems\EzPlatformAdminUi\Form\Type\User\UserDeleteType;
use EzSystems\EzPlatformAdminUi\Form\Type\User\UserEditType;
use EzSystems\EzPlatformAdminUi\Form\Type\Version\VersionRemoveType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Util\StringUtil;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class FormFactory
{
    /** @var \Symfony\Component\Form\FormFactoryInterface */
    private $formFactory;

    /** @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface */
    protected $urlGenerator;

    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    /**
     * @param \Symfony\Component\Form\FormFactoryInterface $formFactory
     * @param \Symfony\Component\Routing\Generator\UrlGeneratorInterface $urlGenerator
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        UrlGeneratorInterface $urlGenerator,
        TranslatorInterface $translator
    ) {
        $this->formFactory = $formFactory;
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\Content\Draft\ContentEditData|null $data
     * @param string|null $name
     * @param array $options
     *
     * @return \Symfony\Component\Form\FormInterface
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function contentEdit(
        ?ContentEditData $data = null,
        ?string $name = null,
        array $options = []
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(ContentEditType::class);
        $data = $data ?? new ContentEditData();

        if (empty($options['language_codes']) && null !== $data->getVersionInfo()) {
            $options['language_codes'] = $data->getVersionInfo()->languageCodes;
        }

        return $this->formFactory->createNamed(
            $name,
            ContentEditType::class,
            $data,
            $options
        );
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\Content\Draft\ContentCreateData|null $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function createContent(
        ?ContentCreateData $data = null,
        ?string $name = null
    ): FormInterface {
        $data = $data ?? new ContentCreateData();
        $name = $name ?: StringUtil::fqcnToBlockPrefix(ContentCreateType::class);

        return $this->formFactory->createNamed($name, ContentCreateType::class, $data);
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\ContentType\ContentTypesDeleteData|null $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function deleteContentTypes(
        ContentTypesDeleteData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(ContentTypesDeleteType::class);

        return $this->formFactory->createNamed($name, ContentTypesDeleteType::class, $data);
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\ContentTypeGroup\ContentTypeGroupCreateData|null $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createContentTypeGroup(
        ?ContentTypeGroupCreateData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(ContentTypeGroupCreateType::class);

        return $this->formFactory->createNamed(
            $name,
            ContentTypeGroupCreateType::class,
            $data ?? new ContentTypeGroupCreateData()
        );
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\ContentTypeGroup\ContentTypeGroupUpdateData|null $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function updateContentTypeGroup(
        ContentTypeGroupUpdateData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: sprintf('update-content-type-group-%d', $data->getContentTypeGroup()->id);

        return $this->formFactory->createNamed($name, ContentTypeGroupUpdateType::class, $data);
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\ContentTypeGroup\ContentTypeGroupDeleteData|null $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function deleteContentTypeGroup(
        ContentTypeGroupDeleteData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: sprintf('delete-content-type-group-%d', $data->getContentTypeGroup()->id);

        return $this->formFactory->createNamed($name, ContentTypeGroupDeleteType::class, $data);
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\ContentTypeGroup\ContentTypeGroupsDeleteData|null $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function deleteContentTypeGroups(
        ContentTypeGroupsDeleteData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(ContentTypeGroupsDeleteType::class);

        return $this->formFactory->createNamed($name, ContentTypeGroupsDeleteType::class, $data);
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\Content\Translation\TranslationAddData|null $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function addTranslation(
        TranslationAddData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: sprintf('add-translation');

        return $this->formFactory->createNamed($name, TranslationAddType::class, $data ?? new TranslationAddData());
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\Content\Translation\TranslationDeleteData|null $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function deleteTranslation(
        TranslationDeleteData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: sprintf('delete-translations');

        return $this->formFactory->createNamed($name, TranslationDeleteType::class, $data);
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\Version\VersionRemoveData|null $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function removeVersion(
        VersionRemoveData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(VersionRemoveType::class);

        return $this->formFactory->createNamed($name, VersionRemoveType::class, $data);
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\Content\Location\ContentLocationAddData|null $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function addLocation(
        ContentLocationAddData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(ContentLocationAddType::class);

        return $this->formFactory->createNamed($name, ContentLocationAddType::class, $data);
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\Content\Location\ContentLocationRemoveData|null $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function removeLocation(
        ContentLocationRemoveData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(ContentLocationRemoveType::class);

        return $this->formFactory->createNamed($name, ContentLocationRemoveType::class, $data);
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationSwapData|null $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function swapLocation(
        LocationSwapData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(LocationSwapType::class);

        return $this->formFactory->createNamed($name, LocationSwapType::class, $data);
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\Content\Location\ContentMainLocationUpdateData|null $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function updateContentMainLocation(
        ?ContentMainLocationUpdateData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(ContentMainLocationUpdateType::class);
        $data = $data ?? new ContentMainLocationUpdateData();

        return $this->formFactory->createNamed(
            $name,
            ContentMainLocationUpdateType::class,
            $data
        );
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationTrashData|null $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function trashLocation(
        LocationTrashData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(LocationTrashType::class);

        return $this->formFactory->createNamed($name, LocationTrashType::class, $data);
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationMoveData|null $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function moveLocation(
        LocationMoveData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(LocationMoveType::class);

        return $this->formFactory->createNamed($name, LocationMoveType::class, $data);
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationCopyData|null $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function copyLocation(
        LocationCopyData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(LocationCopyType::class);

        return $this->formFactory->createNamed($name, LocationCopyType::class, $data);
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationUpdateVisibilityData|null $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function updateVisibilityLocation(
        LocationUpdateVisibilityData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(LocationUpdateVisibilityData::class);

        return $this->formFactory->createNamed($name, LocationUpdateVisibilityType::class, $data);
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationUpdateData|null $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function updateLocation(
        LocationUpdateData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(LocationUpdateType::class);

        return $this->formFactory->createNamed($name, LocationUpdateType::class, $data);
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\Section\SectionContentAssignData|null $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function assignContentSectionForm(
        SectionContentAssignData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(SectionContentAssignType::class);

        return $this->formFactory->createNamed($name, SectionContentAssignType::class, $data);
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\Section\SectionDeleteData|null $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function deleteSection(
        SectionDeleteData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: sprintf('delete-section-%d', $data->getSection()->id);

        return $this->formFactory->createNamed($name, SectionDeleteType::class, $data);
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\Section\SectionsDeleteData|null $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function deleteSections(
        SectionsDeleteData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(SectionsDeleteType::class);

        return $this->formFactory->createNamed($name, SectionsDeleteType::class, $data);
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\Section\SectionCreateData|null $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createSection(
        ?SectionCreateData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(SectionCreateType::class);

        return $this->formFactory->createNamed(
            $name,
            SectionCreateType::class,
            $data ?? new SectionCreateData()
        );
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\Section\SectionUpdateData|null $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function updateSection(
        SectionUpdateData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: sprintf('update-section-%d', $data->getSection()->id);

        return $this->formFactory->createNamed($name, SectionUpdateType::class, $data);
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\Language\LanguageCreateData|null $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createLanguage(
        ?LanguageCreateData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(LanguageCreateType::class);

        return $this->formFactory->createNamed(
            $name,
            LanguageCreateType::class,
            $data ?? new LanguageCreateData()
        );
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\Language\LanguageUpdateData $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function updateLanguage(
        LanguageUpdateData $data,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: sprintf('update-language-%d', $data->getLanguage()->id);

        return $this->formFactory->createNamed($name, LanguageUpdateType::class, $data);
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\Language\LanguageDeleteData $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function deleteLanguage(
        LanguageDeleteData $data,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: sprintf('delete-language-%d', $data->getLanguage()->id);

        return $this->formFactory->createNamed($name, LanguageDeleteType::class, $data);
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\Language\LanguagesDeleteData|null $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function deleteLanguages(
        LanguagesDeleteData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(LanguagesDeleteType::class);

        return $this->formFactory->createNamed($name, LanguagesDeleteType::class, $data);
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\Role\RoleCreateData|null $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createRole(
        ?RoleCreateData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(RoleCreateType::class);

        return $this->formFactory->createNamed($name, RoleCreateType::class, $data);
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\Role\RoleUpdateData $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function updateRole(
        RoleUpdateData $data,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: sprintf('update-role-%d', $data->getRole()->id);

        return $this->formFactory->createNamed($name, RoleUpdateType::class, $data);
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\Role\RoleDeleteData $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function deleteRole(
        RoleDeleteData $data,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: sprintf('delete-role-%d', $data->getRole()->id);

        return $this->formFactory->createNamed($name, RoleDeleteType::class, $data);
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\Role\RolesDeleteData|null $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function deleteRoles(
        RolesDeleteData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: sprintf('delete-roles');

        return $this->formFactory->createNamed($name, RolesDeleteType::class, $data);
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\Role\RoleAssignmentCreateData|null $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createRoleAssignment(
        ?RoleAssignmentCreateData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(RoleAssignmentCreateType::class);

        return $this->formFactory->createNamed(
            $name,
            RoleAssignmentCreateType::class,
            $data ?? new RoleAssignmentCreateData()
        );
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\Role\RoleAssignmentDeleteData $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function deleteRoleAssignment(
        RoleAssignmentDeleteData $data,
        ?string $name = null
    ): FormInterface {
        $role = $data->getRoleAssignment()->getRole()->id;
        $limitation = !empty($data->getRoleAssignment()->getRoleLimitation())
            ? $data->getRoleAssignment()->getRoleLimitation()->getIdentifier()
            : 'none';

        $name = $name ?: sprintf('delete-role-assignment-%s', md5(
            implode('/', [$role, $limitation])
        ));

        return $this->formFactory->createNamed($name, RoleAssignmentDeleteType::class, $data);
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\Role\RoleAssignmentsDeleteData|null $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function deleteRoleAssignments(
        RoleAssignmentsDeleteData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(RoleAssignmentsDeleteType::class);

        return $this->formFactory->createNamed($name, RoleAssignmentsDeleteType::class, $data);
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\Policy\PolicyCreateData|null $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createPolicy(
        ?PolicyCreateData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(PolicyCreateType::class);

        return $this->formFactory->createNamed($name, PolicyCreateType::class, $data);
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\Policy\PolicyCreateData|null $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createPolicyWithLimitation(
        ?PolicyCreateData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(PolicyCreateWithLimitationType::class);

        return $this->formFactory->createNamed($name, PolicyCreateWithLimitationType::class, $data);
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\Policy\PolicyUpdateData $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function updatePolicy(
        PolicyUpdateData $data,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: sprintf('update-policy-%s', md5(implode('/', $data->getPolicy())));

        return $this->formFactory->createNamed($name, PolicyUpdateType::class, $data);
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\Policy\PolicyDeleteData $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function deletePolicy(
        PolicyDeleteData $data,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: sprintf('delete-policy-%s', md5(implode('/', $data->getPolicy())));

        return $this->formFactory->createNamed($name, PolicyDeleteType::class, $data);
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\Policy\PoliciesDeleteData|null $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function deletePolicies(
        PoliciesDeleteData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(PoliciesDeleteType::class);

        return $this->formFactory->createNamed($name, PoliciesDeleteType::class, $data);
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\Search\SearchData|null $data
     * @param string|null $name
     * @param array $options
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createSearchForm(
        SearchData $data = null,
        ?string $name = null,
        array $options = []
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(SearchData::class);

        return $this->formFactory->createNamed($name, SearchType::class, $data, $options);
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\URL\URLListData|null $data
     * @param string|null $name
     * @param array $options
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createUrlListForm(
        URLListData $data = null,
        ?string $name = null,
        array $options = []
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(SearchData::class);

        return $this->formFactory->createNamed($name, URLListType::class, $data, $options);
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\URL\URLUpdateData|null $data
     * @param string|null $name
     * @param array $options
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createUrlEditForm(
        URLUpdateData $data = null,
        ?string $name = null,
        array $options = []
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(SearchData::class);

        return $this->formFactory->createNamed($name, URLEditType::class, $data, $options);
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\User\UserDeleteData|null $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function deleteUser(
        UserDeleteData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(UserDeleteType::class);

        return $this->formFactory->createNamed($name, UserDeleteType::class, $data);
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\Content\CustomUrl\CustomUrlAddData|null $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function addCustomUrl(
        CustomUrlAddData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(CustomUrlAddType::class);

        return $this->formFactory->createNamed($name, CustomUrlAddType::class, $data ?? new CustomUrlAddData());
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\Content\CustomUrl\CustomUrlRemoveData|null $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function removeCustomUrl(
        CustomUrlRemoveData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(CustomUrlRemoveType::class);

        return $this->formFactory->createNamed($name, CustomUrlRemoveType::class, $data ?? new CustomUrlRemoveData());
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\ObjectState\ObjectStateGroupCreateData|null $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createObjectStateGroup(
        ?ObjectStateGroupCreateData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(ObjectStateGroupCreateType::class);

        return $this->formFactory->createNamed(
            $name,
            ObjectStateGroupCreateType::class,
            $data ?? new ObjectStateGroupCreateData()
        );
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\ObjectState\ObjectStateGroupDeleteData|null $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function deleteObjectStateGroup(
        ObjectStateGroupDeleteData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: sprintf('delete-object-state-group-%d', $data->getObjectStateGroup()->id);

        return $this->formFactory->createNamed($name, ObjectStateGroupDeleteType::class, $data);
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\ObjectState\ObjectStateGroupsDeleteData|null $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function deleteObjectStateGroups(
        ObjectStateGroupsDeleteData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(ObjectStateGroupsDeleteType::class);

        return $this->formFactory->createNamed($name, ObjectStateGroupsDeleteType::class, $data);
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\ObjectState\ObjectStateGroupUpdateData|null $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function updateObjectStateGroup(
        ObjectStateGroupUpdateData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: sprintf('update-object-state-group-%d', $data->getObjectStateGroup()->id);

        return $this->formFactory->createNamed($name, ObjectStateGroupUpdateType::class, $data);
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationCopySubtreeData $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function copyLocationSubtree(
        LocationCopySubtreeData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(LocationCopySubtreeType::class);

        return $this->formFactory->createNamed($name, LocationCopySubtreeType::class, $data);
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\Bookmark\BookmarkRemoveData|null $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function removeBookmark(
        BookmarkRemoveData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(BookmarkRemoveType::class);

        return $this->formFactory->createNamed($name, BookmarkRemoveType::class, $data);
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\User\UserEditData|null $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function editUser(
        UserEditData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(UserEditType::class);
        $data = $data ?? new UserEditData();
        $options = null !== $data->getVersionInfo()
            ? ['language_codes' => $data->getVersionInfo()->languageCodes]
            : [];

        return $this->formFactory->createNamed($name, UserEditType::class, $data, $options);
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\Content\Draft\ContentRemoveData|null $data
     * @param string|null $name
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function removeContentDraft(
        ContentRemoveData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(ContentRemoveType::class);

        return $this->formFactory->createNamed($name, ContentRemoveType::class, $data);
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\URLWildcard\URLWildcardData|null $data
     * @param string|null $name
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createURLWildcard(
        ?URLWildcardData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(URLWildcardType::class);

        return $this->formFactory->createNamed(
            $name,
            URLWildcardType::class,
            $data ?? new URLWildcardData()
        );
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\URLWildcard\URLWildcardUpdateData|null $data
     * @param string|null $name
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createURLWildcardUpdate(
        ?URLWildcardUpdateData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(URLWildcardUpdateType::class);

        return $this->formFactory->createNamed(
            $name,
            URLWildcardUpdateType::class,
            $data ?? new URLWildcardUpdateData()
        );
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Data\URLWildcard\URLWildcardDeleteData|null $data
     * @param string|null $name
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function deleteURLWildcard(
        ?URLWildcardDeleteData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(URLWildcardDeleteType::class);

        return $this->formFactory->createNamed(
            $name,
            URLWildcardDeleteType::class,
            $data ?? new URLWildcardDeleteData()
        );
    }
}
