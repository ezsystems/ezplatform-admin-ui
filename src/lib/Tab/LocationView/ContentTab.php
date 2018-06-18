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
use EzSystems\EzPlatformAdminUi\Tab\AbstractTab;
use EzSystems\EzPlatformAdminUi\Tab\OrderedTabInterface;
use EzSystems\EzPlatformAdminUi\Util\FieldDefinitionGroupsUtil;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;

class ContentTab extends AbstractTab implements OrderedTabInterface
{
    /** @var \EzSystems\EzPlatformAdminUi\Util\FieldDefinitionGroupsUtil */
    private $fieldDefinitionGroupsUtil;

    /** @var \eZ\Publish\API\Repository\LanguageService */
    private $languageService;

    /** @var array */
    private $siteAccessLanguages;

    /**
     * @param \Twig\Environment $twig
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     * @param \EzSystems\EzPlatformAdminUi\Util\FieldDefinitionGroupsUtil $fieldDefinitionGroupsUtil
     * @param \eZ\Publish\API\Repository\LanguageService $languageService
     * @param array $siteAccessLanguages
     */
    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        FieldDefinitionGroupsUtil $fieldDefinitionGroupsUtil,
        LanguageService $languageService,
        array $siteAccessLanguages
    ) {
        parent::__construct($twig, $translator);

        $this->fieldDefinitionGroupsUtil = $fieldDefinitionGroupsUtil;
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
        $fieldDefinitionsByGroup = $this->fieldDefinitionGroupsUtil->groupFieldDefinitions($fieldDefinitions);

        $languages = $this->loadContentLanguages($content);

        return $this->twig->render(
            '@ezdesign/content/tab/content.html.twig',
            [
                'content' => $content,
                /* @deprecated since version 2.2, to be removed in 3.0 */
                'fieldDefinitionsByGroup' => $fieldDefinitionsByGroup,
                'field_definitions_by_group' => $fieldDefinitionsByGroup,
                'languages' => $languages,
                'location' => $parameters['location'],
            ]
        );
    }

    /**
     * Loads system languages with filtering applied.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Content $content
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
