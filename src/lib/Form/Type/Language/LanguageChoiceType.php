<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\Language;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ConfigResolver;
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

    /** @var ConfigResolver */
    protected $configResolver;

    /**
     * @param LanguageService $languageService
     */
    public function __construct(LanguageService $languageService, ConfigResolver $configResolver)
    {
        $this->languageService = $languageService;
        $this->configResolver = $configResolver;
    }

    public function getParent()
    {
        return ChoiceType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $languageService = $this->languageService;
        $siteAccessLanguages = $this->configResolver->getParameter('languages');
        $resolver
            ->setDefaults([
                'choice_loader' => new CallbackChoiceLoader(function () use ($languageService, $siteAccessLanguages) {
                    // Order languages by siteaccess languages first, then the rest
                    $siteAccessLanguages[] = null;
                    $languages = [];
                    $repoLanguages = $languageService->loadLanguages();
                    foreach ($siteAccessLanguages as $siteAccessLanguage) {
                        foreach ($repoLanguages as $key => $repoLanguage) {
                            if ($siteAccessLanguage === null) {
                                $languages[] = $repoLanguage;
                            } elseif ($repoLanguage->languageCode === $siteAccessLanguage) {
                                $languages[] = $repoLanguage;
                                unset($repoLanguages[$key]);
                                break;
                            }
                        }
                    }

                    return $languages;
                }),
                'choice_label' => 'name',
                'choice_name' => 'languageCode',
                'choice_value' => 'languageCode',
            ]);
    }
}
