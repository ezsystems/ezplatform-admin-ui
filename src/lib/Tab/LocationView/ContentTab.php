<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab\LocationView;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\Core\Helper\FieldsGroups\FieldsGroupsList;
use EzSystems\EzPlatformAdminUi\Tab\AbstractTab;
use EzSystems\EzPlatformAdminUi\Tab\OrderedTabInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;

class ContentTab extends AbstractTab implements OrderedTabInterface
{
    /** @var FieldsGroupsList */
    private $fieldsGroupsListHelper;

    /** @var LanguageService */
    private $languageService;

    /** @var array */
    private $siteAccessLanguages;

    /**
     * @param Environment $twig
     * @param TranslatorInterface $translator
     * @param FieldsGroupsList $fieldsGroupsListHelper
     * @param LanguageService $languageService
     * @param array $siteAccessLanguages
     */
    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        FieldsGroupsList $fieldsGroupsListHelper,
        LanguageService $languageService,
        array $siteAccessLanguages
    ) {
        parent::__construct($twig, $translator);

        $this->fieldsGroupsListHelper = $fieldsGroupsListHelper;
        $this->languageService = $languageService;
        $this->siteAccessLanguages = $siteAccessLanguages;
    }

    public function getIdentifier(): string
    {
        return 'content';
    }

    public function getName(): string
    {
        /** @Desc("View") */
        return $this->translator->trans('tab.name.view', [], 'locationview');
    }

    public function getOrder(): int
    {
        return 100;
    }

    public function renderView(array $parameters): string
    {
        /** @var Content $content */
        $content = $parameters['content'];
        /** @var ContentType $contentType */
        $contentType = $parameters['contentType'];
        $fieldDefinitions = $contentType->getFieldDefinitions();
        $fieldDefinitionsByGroup = $this->groupFieldDefinitions($fieldDefinitions);

        $languages = $this->loadContentLanguages($content);

        return $this->twig->render(
            '@EzPlatformAdminUi/content/tab/content.html.twig',
            [
                'content' => $content,
                'fieldDefinitionsByGroup' => $fieldDefinitionsByGroup,
                'languages' => $languages,
                'location' => $parameters['location'],
            ]
        );
    }

    /**
     * @param $fieldDefinitions
     *
     * @return mixed
     */
    private function groupFieldDefinitions($fieldDefinitions)
    {
        $fieldDefinitionsByGroup = [];
        foreach ($this->fieldsGroupsListHelper->getGroups() as $groupId => $groupName) {
            $fieldDefinitionsByGroup[$groupId] = [
                'name' => $groupName,
                'fieldDefinitions' => [],
            ];
        }

        foreach ($fieldDefinitions as $fieldDefinition) {
            $groupId = $fieldDefinition->fieldGroup;
            if (!$groupId) {
                $groupId = $this->fieldsGroupsListHelper->getDefaultGroup();
            }

            $fieldDefinitionsByGroup[$groupId]['fieldDefinitions'][] = $fieldDefinition;
        }

        return $fieldDefinitionsByGroup;
    }

    /**
     * Loads system languages with filtering applied.
     *
     * @param Content $content
     *
     * @return array
     */
    public function loadContentLanguages(Content $content): array
    {
        $contentLanguages = $content->versionInfo->languageCodes;

        $filter = function (Language $language) use ($contentLanguages) {
            return $language->enabled && in_array($language->languageCode, $contentLanguages, true);
        };

        $languagesByCode = [];

        foreach (array_filter($this->languageService->loadLanguages(), $filter) as $language) {
            $languagesByCode[$language->languageCode] = $language;
        }

        $saLanguages = [];

        foreach ($this->siteAccessLanguages as $languageCode) {
            if (!isset($languagesByCode[$languageCode])) {
                continue;
            }

            $saLanguages[] = $languagesByCode[$languageCode];
            unset($languagesByCode[$languageCode]);
        }

        return array_merge($saLanguages, array_values($languagesByCode));
    }
}
