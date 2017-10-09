<?php
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\DataTransformer;

use eZ\Publish\API\Repository\LocationService;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Translates Location's ID to domain specific object.
 */
class LocationsTransformer implements DataTransformerInterface
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
        return is_array($value) && !empty($value)
            ? implode(',', array_column($value, 'id'))
            : [];
    }

    public function reverseTransform($value)
    {
        $value = explode(',', $value);

        return !empty($value)
            ? array_map([$this->locationService, 'loadLocation'], $value)
            : null;
    }
}
