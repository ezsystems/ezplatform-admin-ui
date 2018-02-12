<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Factory;

use EzSystems\EzPlatformAdminUi\Form\Data\Content\Draft\ContentCreateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Draft\ContentEditData;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Location\ContentMainLocationUpdateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Location\ContentLocationAddData;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\Location\ContentLocationRemoveData;
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
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationMoveData;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationSwapData;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationUpdateVisibilityData;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationTrashData;
use EzSystems\EzPlatformAdminUi\Form\Data\Location\LocationUpdateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Policy\PoliciesDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\Policy\PolicyCreateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Policy\PolicyDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\Policy\PolicyUpdateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Role\RoleAssignmentsDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\Role\RoleAssignmentCreateData;
use EzSystems\EzPlatformAdminUi\Form\Data\Role\RoleAssignmentDeleteData;
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
use EzSystems\EzPlatformAdminUi\Form\Data\Trash\TrashEmptyData;
use EzSystems\EzPlatformAdminUi\Form\Data\Trash\TrashItemRestoreData;
use EzSystems\EzPlatformAdminUi\Form\Data\User\UserPasswordChangeData;
use EzSystems\EzPlatformAdminUi\Form\Data\User\UserDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Data\Version\VersionRemoveData;
use EzSystems\EzPlatformAdminUi\Form\Type\Content\Draft\ContentCreateType;
use EzSystems\EzPlatformAdminUi\Form\Type\Content\Draft\ContentEditType;
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
use EzSystems\EzPlatformAdminUi\Form\Type\Location\LocationCopyType;
use EzSystems\EzPlatformAdminUi\Form\Type\Location\LocationMoveType;
use EzSystems\EzPlatformAdminUi\Form\Type\Location\LocationSwapType;
use EzSystems\EzPlatformAdminUi\Form\Type\Location\LocationUpdateVisibilityType;
use EzSystems\EzPlatformAdminUi\Form\Type\Location\LocationTrashType;
use EzSystems\EzPlatformAdminUi\Form\Type\Location\LocationUpdateType;
use EzSystems\EzPlatformAdminUi\Form\Type\User\UserPasswordChangeType;
use EzSystems\EzPlatformAdminUi\Form\Type\User\UserDeleteType;
use EzSystems\EzPlatformAdminUi\Form\Type\Policy\PoliciesDeleteType;
use EzSystems\EzPlatformAdminUi\Form\Type\Policy\PolicyCreateType;
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
use EzSystems\EzPlatformAdminUi\Form\Type\Trash\TrashEmptyType;
use EzSystems\EzPlatformAdminUi\Form\Type\Trash\TrashItemRestoreType;
use EzSystems\EzPlatformAdminUi\Form\Type\Version\VersionRemoveType;
use EzSystems\RepositoryForms\Data\URL\URLListData;
use EzSystems\RepositoryForms\Data\URL\URLUpdateData;
use EzSystems\RepositoryForms\Form\Type\URL\URLEditType;
use EzSystems\RepositoryForms\Form\Type\URL\URLListType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Util\StringUtil;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
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
     * @param ContentEditData|null $data
     * @param string|null $name
     *
     * @return FormInterface
     *
     * @throws InvalidOptionsException
     */
    public function contentEdit(
        ?ContentEditData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(ContentEditType::class);
        $data = $data ?? new ContentEditData();
        $options = null !== $data->getVersionInfo()
            ? ['language_codes' => $data->getVersionInfo()->languageCodes]
            : [];

        return $this->formFactory->createNamed(
            $name,
            ContentEditType::class,
            $data,
            $options
        );
    }

    /**
     * @param ContentCreateData|null $data
     * @param string|null $name
     *
     * @return FormInterface
     *
     * @throws InvalidOptionsException
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
     * @param ContentTypesDeleteData|null $data
     * @param null|string $name
     *
     * @return FormInterface
     *
     * @throws InvalidOptionsException
     */
    public function deleteContentTypes(
        ContentTypesDeleteData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(ContentTypesDeleteType::class);

        return $this->formFactory->createNamed($name, ContentTypesDeleteType::class, $data);
    }

    /**
     * @param ContentTypeGroupCreateData|null $data
     * @param null|string $name
     *
     * @return FormInterface
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
     * @param ContentTypeGroupUpdateData|null $data
     * @param null|string $name
     *
     * @return FormInterface
     */
    public function updateContentTypeGroup(
        ContentTypeGroupUpdateData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: sprintf('update-content-type-group-%d', $data->getContentTypeGroup()->id);

        return $this->formFactory->createNamed($name, ContentTypeGroupUpdateType::class, $data);
    }

    /**
     * @param ContentTypeGroupDeleteData|null $data
     * @param null|string $name
     *
     * @return FormInterface
     */
    public function deleteContentTypeGroup(
        ContentTypeGroupDeleteData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: sprintf('delete-content-type-group-%d', $data->getContentTypeGroup()->id);

        return $this->formFactory->createNamed($name, ContentTypeGroupDeleteType::class, $data);
    }

    /**
     * @param ContentTypeGroupsDeleteData|null $data
     * @param null|string $name
     *
     * @return FormInterface
     *
     * @throws InvalidOptionsException
     */
    public function deleteContentTypeGroups(
        ContentTypeGroupsDeleteData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(ContentTypeGroupsDeleteType::class);

        return $this->formFactory->createNamed($name, ContentTypeGroupsDeleteType::class, $data);
    }

    /**
     * @param TranslationAddData|null $data
     * @param null|string $name
     *
     * @return FormInterface
     *
     * @throws InvalidOptionsException
     */
    public function addTranslation(
        TranslationAddData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: sprintf('add-translation');

        return $this->formFactory->createNamed($name, TranslationAddType::class, $data ?? new TranslationAddData());
    }

    /**
     * @param TranslationDeleteData|null $data
     * @param null|string $name
     *
     * @return FormInterface
     *
     * @throws InvalidOptionsException
     */
    public function deleteTranslation(
        TranslationDeleteData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: sprintf('delete-translations');

        return $this->formFactory->createNamed($name, TranslationDeleteType::class, $data);
    }

    /**
     * @param VersionRemoveData|null $data
     * @param null|string $name
     *
     * @return FormInterface
     *
     * @throws InvalidOptionsException
     */
    public function removeVersion(
        VersionRemoveData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(VersionRemoveType::class);

        return $this->formFactory->createNamed($name, VersionRemoveType::class, $data);
    }

    /**
     * @param ContentLocationAddData|null $data
     * @param null|string $name
     *
     * @return FormInterface
     */
    public function addLocation(
        ContentLocationAddData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(ContentLocationAddType::class);

        return $this->formFactory->createNamed($name, ContentLocationAddType::class, $data);
    }

    /**
     * @param ContentLocationRemoveData|null $data
     * @param null|string $name
     *
     * @return FormInterface
     */
    public function removeLocation(
        ContentLocationRemoveData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(ContentLocationRemoveType::class);

        return $this->formFactory->createNamed($name, ContentLocationRemoveType::class, $data);
    }

    /**
     * @param LocationSwapData|null $data
     * @param null|string $name
     *
     * @return FormInterface
     */
    public function swapLocation(
        LocationSwapData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(LocationSwapType::class);

        return $this->formFactory->createNamed($name, LocationSwapType::class, $data);
    }

    /**
     * @param ContentMainLocationUpdateData|null $data
     * @param string|null $name
     *
     * @return FormInterface
     *
     * @throws InvalidOptionsException
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
     * @param LocationTrashData|null $data
     * @param null|string $name
     *
     * @return FormInterface
     */
    public function trashLocation(
        LocationTrashData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(LocationTrashType::class);

        return $this->formFactory->createNamed($name, LocationTrashType::class, $data);
    }

    /**
     * @param LocationMoveData|null $data
     * @param null|string $name
     *
     * @return FormInterface
     */
    public function moveLocation(
        LocationMoveData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(LocationMoveType::class);

        return $this->formFactory->createNamed($name, LocationMoveType::class, $data);
    }

    /**
     * @param LocationCopyData|null $data
     * @param null|string $name
     *
     * @return FormInterface
     */
    public function copyLocation(
        LocationCopyData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(LocationCopyType::class);

        return $this->formFactory->createNamed($name, LocationCopyType::class, $data);
    }

    /**
     * @param LocationUpdateVisibilityData|null $data
     * @param null|string $name
     *
     * @return FormInterface
     *
     * @throws InvalidOptionsException
     */
    public function updateVisibilityLocation(
        LocationUpdateVisibilityData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(LocationUpdateVisibilityData::class);

        return $this->formFactory->createNamed($name, LocationUpdateVisibilityType::class, $data);
    }

    /**
     * @param LocationUpdateData|null $data
     * @param null|string $name
     *
     * @return FormInterface
     */
    public function updateLocation(
        LocationUpdateData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(LocationUpdateType::class);

        return $this->formFactory->createNamed($name, LocationUpdateType::class, $data);
    }

    /**
     * @param TrashItemRestoreData|null $data
     * @param null|string $name
     *
     * @return FormInterface
     */
    public function restoreTrashItem(
        TrashItemRestoreData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(TrashItemRestoreType::class);

        return $this->formFactory->createNamed($name, TrashItemRestoreType::class, $data);
    }

    /**
     * @param TrashEmptyData|null $data
     * @param null|string $name
     *
     * @return FormInterface
     */
    public function emptyTrash(
        TrashEmptyData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(TrashEmptyType::class);

        return $this->formFactory->createNamed($name, TrashEmptyType::class, $data);
    }

    /**
     * @param SectionContentAssignData|null $data
     * @param null|string $name
     *
     * @return FormInterface
     */
    public function assignContentSectionForm(
        SectionContentAssignData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(SectionContentAssignType::class);

        return $this->formFactory->createNamed($name, SectionContentAssignType::class, $data);
    }

    /**
     * @param SectionDeleteData|null $data
     * @param null|string $name
     *
     * @return FormInterface
     */
    public function deleteSection(
        SectionDeleteData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: sprintf('delete-section-%d', $data->getSection()->id);

        return $this->formFactory->createNamed($name, SectionDeleteType::class, $data);
    }

    /**
     * @param SectionsDeleteData|null $data
     * @param null|string $name
     *
     * @return FormInterface
     *
     * @throws InvalidOptionsException
     */
    public function deleteSections(
        SectionsDeleteData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(SectionsDeleteType::class);

        return $this->formFactory->createNamed($name, SectionsDeleteType::class, $data);
    }

    /**
     * @param SectionCreateData|null $data
     * @param null|string $name
     *
     * @return FormInterface
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
     * @param SectionUpdateData|null $data
     * @param null|string $name
     *
     * @return FormInterface
     */
    public function updateSection(
        SectionUpdateData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: sprintf('update-section-%d', $data->getSection()->id);

        return $this->formFactory->createNamed($name, SectionUpdateType::class, $data);
    }

    /**
     * @param LanguageCreateData|null $data
     * @param null|string $name
     *
     * @return FormInterface
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
     * @param LanguageUpdateData $data
     * @param null|string $name
     *
     * @return FormInterface
     */
    public function updateLanguage(
        LanguageUpdateData $data,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: sprintf('update-language-%d', $data->getLanguage()->id);

        return $this->formFactory->createNamed($name, LanguageUpdateType::class, $data);
    }

    /**
     * @param LanguageDeleteData $data
     * @param null|string $name
     *
     * @return FormInterface
     */
    public function deleteLanguage(
        LanguageDeleteData $data,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: sprintf('delete-language-%d', $data->getLanguage()->id);

        return $this->formFactory->createNamed($name, LanguageDeleteType::class, $data);
    }

    /**
     * @param LanguagesDeleteData|null $data
     * @param null|string $name
     *
     * @return FormInterface
     *
     * @throws InvalidOptionsException
     */
    public function deleteLanguages(
        LanguagesDeleteData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(LanguagesDeleteType::class);

        return $this->formFactory->createNamed($name, LanguagesDeleteType::class, $data);
    }

    /**
     * @param RoleCreateData|null $data
     * @param null|string $name
     *
     * @return FormInterface
     */
    public function createRole(
        ?RoleCreateData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(RoleCreateType::class);

        return $this->formFactory->createNamed($name, RoleCreateType::class, $data);
    }

    /**
     * @param RoleUpdateData $data
     * @param null|string $name
     *
     * @return FormInterface
     */
    public function updateRole(
        RoleUpdateData $data,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: sprintf('update-role-%d', $data->getRole()->id);

        return $this->formFactory->createNamed($name, RoleUpdateType::class, $data);
    }

    /**
     * @param RoleDeleteData $data
     * @param null|string $name
     *
     * @return FormInterface
     */
    public function deleteRole(
        RoleDeleteData $data,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: sprintf('delete-role-%d', $data->getRole()->id);

        return $this->formFactory->createNamed($name, RoleDeleteType::class, $data);
    }

    /**
     * @param RolesDeleteData|null $data
     * @param null|string $name
     *
     * @return FormInterface
     *
     * @throws InvalidOptionsException
     */
    public function deleteRoles(
        RolesDeleteData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: sprintf('delete-roles');

        return $this->formFactory->createNamed($name, RolesDeleteType::class, $data);
    }

    /**
     * @param RoleAssignmentCreateData|null $data
     * @param null|string $name
     *
     * @return FormInterface
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
     * @param RoleAssignmentDeleteData $data
     * @param null|string $name
     *
     * @return FormInterface
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
     * @param RoleAssignmentsDeleteData|null $data
     * @param null|string $name
     *
     * @return FormInterface
     *
     * @throws InvalidOptionsException
     */
    public function deleteRoleAssignments(
        RoleAssignmentsDeleteData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(RoleAssignmentsDeleteType::class);

        return $this->formFactory->createNamed($name, RoleAssignmentsDeleteType::class, $data);
    }

    /**
     * @param PolicyCreateData|null $data
     * @param null|string $name
     *
     * @return FormInterface
     */
    public function createPolicy(
        ?PolicyCreateData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(PolicyCreateType::class);

        return $this->formFactory->createNamed($name, PolicyCreateType::class, $data);
    }

    /**
     * @param PolicyUpdateData $data
     * @param null|string $name
     *
     * @return FormInterface
     */
    public function updatePolicy(
        PolicyUpdateData $data,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: sprintf('update-policy-%s', md5(implode('/', $data->getPolicy())));

        return $this->formFactory->createNamed($name, PolicyUpdateType::class, $data);
    }

    /**
     * @param PolicyDeleteData $data
     * @param null|string $name
     *
     * @return FormInterface
     */
    public function deletePolicy(
        PolicyDeleteData $data,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: sprintf('delete-policy-%s', md5(implode('/', $data->getPolicy())));

        return $this->formFactory->createNamed($name, PolicyDeleteType::class, $data);
    }

    /**
     * @param PoliciesDeleteData|null $data
     * @param null|string $name
     *
     * @return FormInterface
     *
     * @throws InvalidOptionsException
     */
    public function deletePolicies(
        PoliciesDeleteData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(PoliciesDeleteType::class);

        return $this->formFactory->createNamed($name, PoliciesDeleteType::class, $data);
    }

    /**
     * @param SearchData|null $data
     * @param null|string $name
     * @param array $options
     *
     * @return FormInterface
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
     * @param URLListData|null $data
     * @param null|string $name
     * @param array $options
     *
     * @return FormInterface
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
     * @param URLUpdateData|null $data
     * @param null|string $name
     * @param array $options
     *
     * @return FormInterface
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
     * @param UserDeleteData|null $data
     * @param null|string $name
     *
     * @return FormInterface
     */
    public function deleteUser(
        UserDeleteData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(UserDeleteType::class);

        return $this->formFactory->createNamed($name, UserDeleteType::class, $data);
    }

    /**
     * @param UserPasswordChangeData $data
     * @param null|string $name
     *
     * @return FormInterface
     *
     * @throws InvalidOptionsException
     */
    public function changeUserPassword(
        UserPasswordChangeData $data = null,
        ?string $name = null
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(UserPasswordChangeType::class);

        return $this->formFactory->createNamed($name, UserPasswordChangeType::class, $data);
    }
}
