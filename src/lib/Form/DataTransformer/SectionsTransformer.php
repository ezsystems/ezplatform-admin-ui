<?php
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\DataTransformer;

use eZ\Publish\API\Repository\SectionService;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Translates Sections ID to domain specific object.
 */
class SectionsTransformer implements DataTransformerInterface
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
        return is_array($value) && !empty($value)
            ? implode(',', array_column($value, 'id'))
            : [];
    }

    public function reverseTransform($value)
    {
        $value = explode(',', $value);

        return !empty($value)
            ? array_map([$this->sectionService, 'loadSection'], $value)
            : null;
    }
}
