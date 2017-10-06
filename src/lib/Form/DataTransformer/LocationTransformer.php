<?php
declare(strict_types=1);

namespace EzPlatformAdminUi\Form\DataTransformer;

use eZ\Publish\API\Repository\LocationService;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Translates Location's ID to domain specific object.
 */
class LocationTransformer implements DataTransformerInterface
{
    /** @var LocationService */
    protected $locationService;

    /**
     * @param LocationService $locationService
     */
    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
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
            ? $this->locationService->loadLocation($value)
            : null;
    }
}
