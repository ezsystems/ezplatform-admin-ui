<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\Language;

use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\Values\Content\Language;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form Type allowing to select Language.
 *
 * @todo: This can replace LanguageType in the future but it'd require some changes in the frontend as well.
 */
class LimitedLanguageChoiceType extends AbstractType
{
    /** @var LanguageService */
    protected $languageService;

    /** @var string[] */
    protected $siteaccessLanguages;

    /** @var PermissionResolver */
    protected $permissionResolver;

    /**
     * @param LanguageService $languageService
     * @param string[] $siteaccessLanguages
     * @param PermissionResolver $permissionResolver
     */
    public function __construct(
        LanguageService $languageService,
        array $siteaccessLanguages,
        PermissionResolver $permissionResolver
    ) {
        $this->languageService = $languageService;
        $this->siteaccessLanguages = $siteaccessLanguages;
        $this->permissionResolver = $permissionResolver;
    }

    public function getParent()
    {
        return ChoiceType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'choice_loader' => $this->getChoiceLoader(),
                'choice_label' => 'name',
                'choice_name' => 'languageCode',
                'choice_value' => 'languageCode',
            ]);
    }

    /**
     * @return CallbackChoiceLoader
     */
    private function getChoiceLoader(): CallbackChoiceLoader
    {
        return new CallbackChoiceLoader(
            function () {
                $systemLanguages = $this->languageService->loadLanguages();
                $siteaccessLanguages = $this->siteaccessLanguages;
                $availableLanguageCodes = array_intersect(
                    array_column($systemLanguages, 'languageCode'),
                    $siteaccessLanguages
                );

                $languages = array_filter($systemLanguages, function (Language $language) use ($availableLanguageCodes) {
                    return $language->enabled && in_array($language->languageCode, $availableLanguageCodes, true);
                });

                return $languages;
            }
        );
    }
}
