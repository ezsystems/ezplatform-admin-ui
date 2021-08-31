<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\ChoiceList\Loader;

use Symfony\Contracts\Translation\TranslatorInterface;

class DatePeriodChoiceLoader extends BaseChoiceLoader
{
    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @inheritdoc
     */
    public function getChoiceList(): array
    {
        $choices = [];
        foreach ($this->getDatePeriods() as $label => $value) {
            $choices[$label] = $value;
        }

        return $choices;
    }

    private function getDatePeriods(): array
    {
        return [
            $this->translator->trans(/** @Desc("Last week") */ 'date_period_choice.last_week', [], 'date_period') => 'P0Y0M7D',
            $this->translator->trans(/** @Desc("Last month") */ 'date_period_choice.last_month', [], 'date_period') => 'P0Y1M0D',
            $this->translator->trans(/** @Desc("Last year") */ 'date_period_choice.last_year', [], 'date_period') => 'P1Y0M0D',
            $this->translator->trans(/** @Desc("Custom range") */ 'date_period_choice.custom_range', [], 'date_period') => 'custom_range',
        ];
    }
}
