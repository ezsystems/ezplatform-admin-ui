<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Validator\Constraints;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\Values\Content\LanguageCreateStruct;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Will check if Language code is not already used in the content repository.
 */
class UniqueLanguageCodeValidator extends ConstraintValidator
{
    /**
     * @var LanguageService
     */
    private $languageService;

    public function __construct(LanguageService $languageService)
    {
        $this->languageService = $languageService;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param LanguageCreateStruct $value The value that should be validated
     * @param Constraint|UniqueFieldDefinitionIdentifier $constraint The constraint for the validation
     *
     * @api
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof LanguageCreateStruct) {
            return;
        }

        try {
            $language = $this->languageService->loadLanguage($value->languageCode);
            if ($language->id == $value->getId()) {
                return;
            }

            $this->context->buildViolation($constraint->message)
                ->atPath('language_code')
                ->setParameter('%language_code%', $value->languageCode)
                ->addViolation();
        } catch (NotFoundException $e) {
            // Do nothing
        } catch (InvalidArgumentException $e) {
            // Do nothing
        }
    }
}
