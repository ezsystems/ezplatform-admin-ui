<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\DataTransformer;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\Values\Content\Language;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Transforms between a Language's ID and a domain specific Language object.
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

    /**
     * Transforms a domain specific Language object into a Language's ID.
     *
     * @param Language|null $value
     *
     * @return string|null
     *
     * @throws TransformationFailedException if the given value is not a Language object
     */
    public function transform($value)
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof Language) {
            throw new TransformationFailedException('Expected a ' . Language::class . ' object.');
        }

        return $value->languageCode;
    }

    /**
     * Transforms a Content's ID integer into a domain specific ContentInfo object.
     *
     * @param string|null $value
     *
     * @return Language|null
     *
     * @throws NotFoundException
     * @throws TransformationFailedException if the value can not be found
     */
    public function reverseTransform($value)
    {
        if (empty($value)) {
            return null;
        }

        if (!is_string($value)) {
            throw new TransformationFailedException(
                'Invalid data, expected value type is string'
            );
        }

        try {
            return $this->languageService->loadLanguage($value);
        } catch (NotFoundException $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
