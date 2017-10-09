<?php
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\DataTransformer;

use eZ\Publish\API\Repository\LanguageService;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Translates Language's ID to domain specific Language object.
 */
class LanguageTransformer implements DataTransformerInterface
{
    /** @var LanguageService */
    protected $languageService;

    /**
     * @param LanguageService $languageService
     */
    public function __construct(LanguageService $languageService)
    {
        $this->languageService = $languageService;
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
            ? $this->languageService->loadLanguageById($value)
            : null;
    }
}
