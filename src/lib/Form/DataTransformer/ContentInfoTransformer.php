<?php
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\DataTransformer;

use eZ\Publish\API\Repository\ContentService;
use Symfony\Component\Form\DataTransformerInterface;

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

    public function transform($value)
    {
        return null !== $value
            ? $value->id
            : null;
    }

    public function reverseTransform($value)
    {
        return null !== $value && !empty($value)
            ? $this->contentService->loadContentInfo($value)
            : null;
    }
}
