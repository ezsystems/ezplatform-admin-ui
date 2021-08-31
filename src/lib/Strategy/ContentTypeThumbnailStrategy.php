<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Strategy;

use eZ\Publish\API\Repository\Values\Content\Thumbnail;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\SPI\Repository\Strategy\ContentThumbnail\ThumbnailStrategy;
use EzSystems\EzPlatformAdminUi\Exception\ContentTypeIconNotFoundException;
use EzSystems\EzPlatformAdminUi\UI\Service\ContentTypeIconResolver;

final class ContentTypeThumbnailStrategy implements ThumbnailStrategy
{
    private const THUMBNAIL_MIME_TYPE = 'image/svg+xml';

    /** @var \EzSystems\EzPlatformAdminUi\UI\Service\ContentTypeIconResolver */
    private $contentTypeIconResolver;

    public function __construct(
        ContentTypeIconResolver $contentTypeIconResolver
    ) {
        $this->contentTypeIconResolver = $contentTypeIconResolver;
    }

    public function getThumbnail(
        ContentType $contentType,
        array $fields,
        ?VersionInfo $versionInfo = null
    ): ?Thumbnail {
        try {
            $contentTypeIcon = $this->contentTypeIconResolver->getContentTypeIcon($contentType->identifier);

            return new Thumbnail([
                'resource' => $contentTypeIcon,
                'mimeType' => self::THUMBNAIL_MIME_TYPE,
            ]);
        } catch (ContentTypeIconNotFoundException $exception) {
            return null;
        }
    }
}
