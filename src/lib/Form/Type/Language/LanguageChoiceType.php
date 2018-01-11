<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\Language;

use eZ\Publish\API\Repository\LanguageService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form Type allowing to select Language.
 *
 * @todo: This can replace LanguageType in the future but it'd require some changes in the frontend as well.
 */
class LanguageChoiceType extends AbstractType
{
    /** @var LanguageService */
    protected $languageService;

    /** @var array */
    protected $siteAccessLanguages;

    /**
     * @param LanguageService $languageService
     * @param array $siteAccessLanguages
     */
    public function __construct(LanguageService $languageService, array $siteAccessLanguages)
    {
        $this->languageService = $languageService;
        $this->siteAccessLanguages = $siteAccessLanguages;
    }

    public function getParent()
    {
        return ChoiceType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'choice_loader' => new CallbackChoiceLoader(function() {
                    $saLanguages = [];
                    $languagesByCode = [];

                    foreach ($this->languageService->loadLanguages() as $language) {
                        if ($language->enabled) {
                            $languagesByCode[$language->languageCode] = $language;
                        }
                    }

                    foreach ($this->siteAccessLanguages as $languageCode) {
                        if (!isset($languagesByCode[$languageCode])) {
                            continue;
                        }

                        $saLanguages[] = $languagesByCode[$languageCode];
                        unset($languagesByCode[$languageCode]);
                    }

                    return array_merge($saLanguages, array_values($languagesByCode));
                }),
                'choice_label' => 'name',
                'choice_name' => 'languageCode',
                'choice_value' => 'languageCode',
            ]);
    }
}
