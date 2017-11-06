<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\DataTransformer;

use eZ\Publish\API\Repository\LanguageService;
use Symfony\Component\Form\DataTransformerInterface;
use eZ\Publish\API\Repository\Values\Content\Language;
use Symfony\Component\Form\Exception\TransformationFailedException;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;

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
     * @return mixed|null
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

        return $value->id;
    }

    /**
     * Transforms a Content's ID integer into a domain specific ContentInfo object.
     *
     * @param mixed $value
     *
     * @return Language|mixed|null
     *
     * @throws TransformationFailedException if the value can not be found
     */
    public function reverseTransform($value)
    {
        if (empty($value)) {
            return null;
        }

        try {
            return $this->languageService->loadLanguageById($value);
        } catch (NotFoundException $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
