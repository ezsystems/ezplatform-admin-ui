<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Search;

use Pagerfanta\Pagerfanta;

class PagerContentToDataMapper extends AbstractPagerContentToDataMapper
{
    /**
     * @param Pagerfanta $pager
     *
     * @return array
     */
    public function map(Pagerfanta $pager): array
    {
        $data = [];
        $contentTypeIds = [];

        foreach ($pager as $content) {
            /** @var \eZ\Publish\API\Repository\Values\Content\Content $content */
            $contentInfo = $content->getVersionInfo()->getContentInfo();

            $contentTypeIds[] = $contentInfo->contentTypeId;
            $data[] = [
                'content' => $content,
                'contentTypeId' => $contentInfo->contentTypeId,
                'contentId' => $content->id,
                'name' => $this->translationHelper->getTranslatedContentName($content),
                'language' => $contentInfo->mainLanguageCode,
                'contributor' => $this->getContributor($contentInfo),
                'version' => $content->versionInfo->versionNo,
                'content_type' => $content->getContentType(),
                'modified' => $content->versionInfo->modificationDate,
                'initialLanguageCode' => $content->versionInfo->initialLanguageCode,
                'content_is_user' => $this->isContentIsUser($content),
                'available_enabled_translations' => $this->getAvailableTranslations($content, true),
            ];
        }

        $this->setTranslatedContentTypesNames($data, $contentTypeIds);

        return $data;
    }
}
