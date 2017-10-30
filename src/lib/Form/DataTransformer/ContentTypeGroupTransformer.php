<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\DataTransformer;

use eZ\Publish\API\Repository\ContentTypeService;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Translates ContentTypeGroup's ID to domain specific ContentTypeGroup object.
 */
class ContentTypeGroupTransformer implements DataTransformerInterface
{
    /** @var ContentTypeService */
    protected $contentTypeService;

    /**
     * @param ContentTypeService $contentTypeService
     */
    public function __construct(ContentTypeService $contentTypeService)
    {
        $this->contentTypeService = $contentTypeService;
    }

    public function transform($value)
    {
        return null !== $value
            ? $value->id
            : null;
    }

    public function reverseTransform($value)
    {
        return null !== $value
            ? $this->contentTypeService->loadContentTypeGroup($value)
            : null;
    }
}
