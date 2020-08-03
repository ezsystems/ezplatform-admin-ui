<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Search;

use Pagerfanta\Pagerfanta;

class PagerSearchContentToDataMapper extends AbstractPagerContentToDataMapper
{
    /**
     * @param \Pagerfanta\Pagerfanta $pager
     *
     * @return array
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function map(Pagerfanta $pager): array
    {
        $data = [];
        $contentTypeIds = [];

        /** @var \eZ\Publish\API\Repository\Values\Content\Search\SearchHit $searchHit */
        foreach ($pager as $searchHit) {
            /** @var \eZ\Publish\API\Repository\Values\Content\Content $content */
            $content = $searchHit->valueObject;
            $contentInfo = $content->contentInfo;

            $contentTypeIds[] = $contentInfo->contentTypeId;
            $data[] = [
                'content' => $content,
                'contentTypeId' => $contentInfo->contentTypeId,
                'contentId' => $content->id,
                'mainLocationId' => $contentInfo->mainLocationId,
                'name' => $this->translationHelper->getTranslatedContentName(
                    $content,
                    $searchHit->matchedTranslation
                ),
                'language' => $contentInfo->mainLanguageCode,
                'contributor' => $this->getContributor($contentInfo),
                'version' => $content->versionInfo->versionNo,
                'content_type' => $content->getContentType(),
                'modified' => $content->versionInfo->modificationDate,
                'initialLanguageCode' => $content->versionInfo->initialLanguageCode,
                'content_is_user' => $this->isContentIsUser($content),
                'available_enabled_translations' => $this->getAvailableTranslations($content, true),
                'available_translations' => $this->getAvailableTranslations($content),
                'translation_language_code' => $searchHit->matchedTranslation,
                'resolvedLocation' => $this->locationResolver->resolveLocation($contentInfo),
            ];
        }

        $this->setTranslatedContentTypesNames($data, $contentTypeIds);

        return $data;
    }
}
