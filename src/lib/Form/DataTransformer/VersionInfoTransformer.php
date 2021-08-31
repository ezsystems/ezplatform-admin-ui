<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\DataTransformer;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Translates Content's ID to domain specific VersionInfo object.
 */
final class VersionInfoTransformer implements DataTransformerInterface
{
    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    public function __construct(ContentService $contentService)
    {
        $this->contentService = $contentService;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\VersionInfo|null $value
     */
    public function transform($value): ?array
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof VersionInfo) {
            throw new TransformationFailedException(
                'Value cannot be transformed because the passed value is not a VersionInfo object'
            );
        }

        return [
            'content_info' => $value->getContentInfo(),
            'version_no' => $value->versionNo,
        ];
    }

    /**
     * @param array|null $value
     */
    public function reverseTransform($value): ?VersionInfo
    {
        if (null === $value || !is_array($value)) {
            return null;
        }

        if (!array_key_exists('content_info', $value) || !array_key_exists('version_no', $value)) {
            throw new TransformationFailedException(
                "Invalid data. Value array is missing 'content_info' and/or 'version_no' keys"
            );
        }

        if (!($value['content_info'] instanceof ContentInfo) || null === $value['version_no']) {
            return null;
        }

        try {
            return $this->contentService->loadVersionInfo($value['content_info'], (int)$value['version_no']);
        } catch (NotFoundException | UnauthorizedException $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
