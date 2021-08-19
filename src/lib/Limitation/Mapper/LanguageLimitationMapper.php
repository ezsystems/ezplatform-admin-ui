<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\AdminUi\Limitation\Mapper;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\Values\User\Limitation;
use Ibexa\AdminUi\Limitation\LimitationValueMapperInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

class LanguageLimitationMapper extends MultipleSelectionBasedMapper implements LimitationValueMapperInterface
{
    use LoggerAwareTrait;

    /**
     * @var LanguageService
     */
    private $languageService;

    public function __construct(LanguageService $languageService)
    {
        $this->languageService = $languageService;
        $this->logger = new NullLogger();
    }

    protected function getSelectionChoices()
    {
        $choices = [];
        foreach ($this->languageService->loadLanguages() as $language) {
            $choices[$language->languageCode] = $language->name;
        }

        return $choices;
    }

    public function mapLimitationValue(Limitation $limitation)
    {
        $values = [];

        foreach ($limitation->limitationValues as $languageCode) {
            try {
                $values[] = $this->languageService->loadLanguage($languageCode);
            } catch (NotFoundException $e) {
                $this->logger->error(sprintf('Could not map the Limitation value: could not find a language with code %s', $languageCode));
            }
        }

        return $values;
    }
}

class_alias(LanguageLimitationMapper::class, 'EzSystems\EzPlatformAdminUi\Limitation\Mapper\LanguageLimitationMapper');
