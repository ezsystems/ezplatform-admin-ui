<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Form\Factory;

use Ibexa\AdminUi\Form\Data\Bookmark\BookmarkRemoveData;
use Ibexa\AdminUi\Form\Data\Content\CustomUrl\CustomUrlAddData;
use Ibexa\AdminUi\Form\Data\Content\CustomUrl\CustomUrlRemoveData;
use Ibexa\AdminUi\Form\Data\Content\Draft\ContentCreateData;
use Ibexa\AdminUi\Form\Data\Content\Draft\ContentEditData;
use Ibexa\AdminUi\Form\Data\Content\Draft\ContentRemoveData;
use Ibexa\AdminUi\Form\Data\Content\Location\ContentLocationAddData;
use Ibexa\AdminUi\Form\Data\Content\Location\ContentLocationRemoveData;
use Ibexa\AdminUi\Form\Data\Content\Location\ContentMainLocationUpdateData;
use Ibexa\AdminUi\Form\Data\Content\Translation\TranslationAddData;
use Ibexa\AdminUi\Form\Data\Content\Translation\TranslationDeleteData;
use Ibexa\AdminUi\Form\Data\ContentType\ContentTypesDeleteData;
use Ibexa\AdminUi\Form\Data\ContentTypeGroup\ContentTypeGroupCreateData;
use Ibexa\AdminUi\Form\Data\ContentTypeGroup\ContentTypeGroupDeleteData;
use Ibexa\AdminUi\Form\Data\ContentTypeGroup\ContentTypeGroupsDeleteData;
use Ibexa\AdminUi\Form\Data\ContentTypeGroup\ContentTypeGroupUpdateData;
use Ibexa\AdminUi\Form\Data\Language\LanguageCreateData;
use Ibexa\AdminUi\Form\Data\Language\LanguageDeleteData;
use Ibexa\AdminUi\Form\Data\Language\LanguagesDeleteData;
use Ibexa\AdminUi\Form\Data\Language\LanguageUpdateData;
use Ibexa\AdminUi\Form\Data\Location\LocationCopyData;
use Ibexa\AdminUi\Form\Data\Location\LocationCopySubtreeData;
use Ibexa\AdminUi\Form\Data\Location\LocationMoveData;
use Ibexa\AdminUi\Form\Data\Location\LocationSwapData;
use Ibexa\AdminUi\Form\Data\Location\LocationTrashData;
use Ibexa\AdminUi\Form\Data\Location\LocationUpdateData;
use Ibexa\AdminUi\Form\Data\Location\LocationUpdateVisibilityData;
use Ibexa\AdminUi\Form\Data\ObjectState\ObjectStateGroupCreateData;
use Ibexa\AdminUi\Form\Data\ObjectState\ObjectStateGroupDeleteData;
use Ibexa\AdminUi\Form\Data\ObjectState\ObjectStateGroupsDeleteData;
use Ibexa\AdminUi\Form\Data\ObjectState\ObjectStateGroupUpdateData;
use Ibexa\AdminUi\Form\Data\Policy\PoliciesDeleteData;
use Ibexa\AdminUi\Form\Data\Policy\PolicyCreateData;
use Ibexa\AdminUi\Form\Data\Policy\PolicyDeleteData;
use Ibexa\AdminUi\Form\Data\Policy\PolicyUpdateData;
use Ibexa\AdminUi\Form\Data\Role\RoleAssignmentCreateData;
use Ibexa\AdminUi\Form\Data\Role\RoleAssignmentDeleteData;
use Ibexa\AdminUi\Form\Data\Role\RoleAssignmentsDeleteData;
use Ibexa\AdminUi\Form\Data\Role\RoleCreateData;
use Ibexa\AdminUi\Form\Data\Role\RoleDeleteData;
use Ibexa\AdminUi\Form\Data\Role\RolesDeleteData;
use Ibexa\AdminUi\Form\Data\Role\RoleUpdateData;
use Ibexa\AdminUi\Form\Data\Search\SearchData;
use Ibexa\AdminUi\Form\Data\Section\SectionContentAssignData;
use Ibexa\AdminUi\Form\Data\Section\SectionCreateData;
use Ibexa\AdminUi\Form\Data\Section\SectionDeleteData;
use Ibexa\AdminUi\Form\Data\Section\SectionsDeleteData;
use Ibexa\AdminUi\Form\Data\Section\SectionUpdateData;
use Ibexa\AdminUi\Form\Data\URLWildcard\URLWildcardData;
use Ibexa\AdminUi\Form\Data\URLWildcard\URLWildcardDeleteData;
use Ibexa\AdminUi\Form\Data\URLWildcard\URLWildcardUpdateData;
use Ibexa\AdminUi\Form\Data\User\UserDeleteData;
use Ibexa\AdminUi\Form\Data\User\UserEditData;
use Ibexa\AdminUi\Form\Data\Version\VersionRemoveData;
use Ibexa\AdminUi\Form\Type\Bookmark\BookmarkRemoveType;
use Ibexa\AdminUi\Form\Type\Content\CustomUrl\CustomUrlAddType;
use Ibexa\AdminUi\Form\Type\Content\CustomUrl\CustomUrlRemoveType;
use Ibexa\AdminUi\Form\Type\Content\Draft\ContentCreateType;
use Ibexa\AdminUi\Form\Type\Content\Draft\ContentEditType;
use Ibexa\AdminUi\Form\Type\Content\Draft\ContentRemoveType;
use Ibexa\AdminUi\Form\Type\Content\Location\ContentLocationAddType;
use Ibexa\AdminUi\Form\Type\Content\Location\ContentLocationRemoveType;
use Ibexa\AdminUi\Form\Type\Content\Location\ContentMainLocationUpdateType;
use Ibexa\AdminUi\Form\Type\Content\Translation\TranslationAddType;
use Ibexa\AdminUi\Form\Type\Content\Translation\TranslationDeleteType;
use Ibexa\AdminUi\Form\Type\ContentType\ContentTypesDeleteType;
use Ibexa\AdminUi\Form\Type\ContentTypeGroup\ContentTypeGroupCreateType;
use Ibexa\AdminUi\Form\Type\ContentTypeGroup\ContentTypeGroupDeleteType;
use Ibexa\AdminUi\Form\Type\ContentTypeGroup\ContentTypeGroupsDeleteType;
use Ibexa\AdminUi\Form\Type\ContentTypeGroup\ContentTypeGroupUpdateType;
use Ibexa\AdminUi\Form\Type\Language\LanguageCreateType;
use Ibexa\AdminUi\Form\Type\Language\LanguageDeleteType;
use Ibexa\AdminUi\Form\Type\Language\LanguagesDeleteType;
use Ibexa\AdminUi\Form\Type\Language\LanguageUpdateType;
use Ibexa\AdminUi\Form\Type\Location\LocationCopySubtreeType;
use Ibexa\AdminUi\Form\Type\Location\LocationCopyType;
use Ibexa\AdminUi\Form\Type\Location\LocationMoveType;
use Ibexa\AdminUi\Form\Type\Location\LocationSwapType;
use Ibexa\AdminUi\Form\Type\Location\LocationTrashType;
use Ibexa\AdminUi\Form\Type\Location\LocationUpdateType;
use Ibexa\AdminUi\Form\Type\Location\LocationUpdateVisibilityType;
use Ibexa\AdminUi\Form\Type\ObjectState\ObjectStateGroupCreateType;
use Ibexa\AdminUi\Form\Type\ObjectState\ObjectStateGroupDeleteType;
use Ibexa\AdminUi\Form\Type\ObjectState\ObjectStateGroupsDeleteType;
use Ibexa\AdminUi\Form\Type\ObjectState\ObjectStateGroupUpdateType;
use Ibexa\AdminUi\Form\Type\Policy\PoliciesDeleteType;
use Ibexa\AdminUi\Form\Type\Policy\PolicyCreateType;
use Ibexa\AdminUi\Form\Type\Policy\PolicyCreateWithLimitationType;
use Ibexa\AdminUi\Form\Type\Policy\PolicyDeleteType;
use Ibexa\AdminUi\Form\Type\Policy\PolicyUpdateType;
use Ibexa\AdminUi\Form\Type\Role\RoleAssignmentCreateType;
use Ibexa\AdminUi\Form\Type\Role\RoleAssignmentDeleteType;
use Ibexa\AdminUi\Form\Type\Role\RoleAssignmentsDeleteType;
use Ibexa\AdminUi\Form\Type\Role\RoleCreateType;
use Ibexa\AdminUi\Form\Type\Role\RoleDeleteType;
use Ibexa\AdminUi\Form\Type\Role\RolesDeleteType;
use Ibexa\AdminUi\Form\Type\Role\RoleUpdateType;
use Ibexa\AdminUi\Form\Type\Search\SearchType;
use Ibexa\AdminUi\Form\Type\Section\SectionContentAssignType;
use Ibexa\AdminUi\Form\Type\Section\SectionCreateType;
use Ibexa\AdminUi\Form\Type\Section\SectionDeleteType;
use Ibexa\AdminUi\Form\Type\Section\SectionsDeleteType;
use Ibexa\AdminUi\Form\Type\Section\SectionUpdateType;
use Ibexa\AdminUi\Form\Type\URLWildcard\URLWildcardType;
use Ibexa\AdminUi\Form\Type\URLWildcard\URLWildcardDeleteType;
use Ibexa\AdminUi\Form\Type\URLWildcard\URLWildcardUpdateType;
use Ibexa\AdminUi\Form\Type\User\UserDeleteType;
use Ibexa\AdminUi\Form\Type\User\UserEditType;
use Ibexa\AdminUi\Form\Type\Version\VersionRemoveType;
use Ibexa\AdminUi\Form\Data\URL\URLListData;
use Ibexa\AdminUi\Form\Data\URL\URLUpdateData;
use Ibexa\AdminUi\Form\Type\URL\URLEditType;
use Ibexa\AdminUi\Form\Type\URL\URLListType;
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

class_alias(FormFactory::class, 'EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory');
