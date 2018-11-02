<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\Language;

use EzSystems\EzPlatformAdminUi\Form\Type\ChoiceList\Provider\LanguageChoiceListProvider;
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
    /** @var \EzSystems\EzPlatformAdminUi\Form\Type\ChoiceList\Provider\LanguageChoiceListProvider */
    private $languageChoiceListProvider;

    /**
     * @param \EzSystems\EzPlatformAdminUi\Form\Type\ChoiceList\Provider\LanguageChoiceListProvider $languageChoiceListProvider
     */
    public function __construct(LanguageChoiceListProvider $languageChoiceListProvider)
    {
        $this->languageChoiceListProvider = $languageChoiceListProvider;
    }

    public function getParent()
    {
        return ChoiceType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'choice_loader' => new CallbackChoiceLoader([$this->languageChoiceListProvider, 'getChoiceList']),
                'choice_label' => 'name',
                'choice_name' => 'languageCode',
                'choice_value' => 'languageCode',
            ]);
    }
}
