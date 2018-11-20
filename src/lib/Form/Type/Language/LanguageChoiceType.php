<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\Language;

use EzSystems\EzPlatformAdminUi\Form\Type\ChoiceList\Loader\LanguageChoiceLoader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form Type allowing to select Language.
 *
 * @todo: This can replace LanguageType in the future but it'd require some changes in the frontend as well.
 */
class LanguageChoiceType extends AbstractType
{
    /** @var \EzSystems\EzPlatformAdminUi\Form\Type\ChoiceList\Loader\LanguageChoiceLoader */
    private $languageChoiceLoader;

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Type\ChoiceList\Loader\LanguageChoiceLoader $languageChoiceLoader
     */
    public function __construct(LanguageChoiceLoader $languageChoiceLoader)
    {
        $this->languageChoiceLoader = $languageChoiceLoader;
    }

    public function getParent()
    {
        return ChoiceType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'choice_loader' => $this->languageChoiceLoader,
                'choice_label' => 'name',
                'choice_name' => 'languageCode',
                'choice_value' => 'languageCode',
            ]);
    }
}
