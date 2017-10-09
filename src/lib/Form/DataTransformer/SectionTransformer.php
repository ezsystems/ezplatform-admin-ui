<?php
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\DataTransformer;

use eZ\Publish\API\Repository\SectionService;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Translates Section's ID to domain specific object.
 */
class SectionTransformer implements DataTransformerInterface
{
    /** @var SectionService */
    protected $sectionService;

    /**
     * @param SectionService $sectionService
     */
    public function __construct(SectionService $sectionService)
    {
        $this->sectionService = $sectionService;
    }

    public function transform($value)
    {
        return null !== $value
            ? $value->id
            : null;
    }

    public function reverseTransform($value)
    {
        return !empty($value)
            ? $this->sectionService->loadSection($value)
            : null;
    }
}
