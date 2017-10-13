<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\DataTransformer;

use eZ\Publish\API\Repository\ContentService;
use Symfony\Component\Form\DataTransformerInterface;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Translates Content's ID to domain specific ContentInfo object.
 */
class ContentInfoTransformer implements DataTransformerInterface
{
    /** @var ContentService */
    protected $contentService;

    /**
     * @param ContentService $contentService
     */
    public function __construct(ContentService $contentService)
    {
        $this->contentService = $contentService;
    }

    /**
     * @param null|ContentInfo $value
     * @return mixed|null
     */
    public function transform($value)
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof ContentInfo) {
            throw new TransformationFailedException('Expected an ' . ContentInfo::class . ' object.');
        }

        return $value->id;
    }

    /**
     * @param mixed $value
     * @return ContentInfo|null
     */
    public function reverseTransform($value)
    {
        if (null === $value || empty($value)) {
            return null;
        }

        if (!is_int($value)) {
            throw new TransformationFailedException('Expected an integer.');
        }

        return $this->contentService->loadContentInfo($value);
    }
}
