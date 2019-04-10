<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

use DateTime;
use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields\Time;

class DateAndTimePopup extends Element
{
    public const ELEMENT_NAME = 'Date and time popup';

    private const DATETIME_FORMAT = 'd/m/Y';

    private const SETTING_SCRIPT_FORMAT = "document.querySelector('%s %s')._flatpickr.setDate('%s', true, '%s')";

    private const ADD_CALLBACK_TO_DATEPICKER_SCRIPT_FORMAT = 'var fi = document.querySelector(\'%s .flatpickr-input\');
                const onChangeOld = fi._flatpickr.config.onChange;
                const onChangeNew = (dates, dateString, flatpickInstance) => {
                flatpickInstance.input.classList.add("date-set");
            };
        if (onChangeOld instanceof Array) {
                fi._flatpickr.config.onChange = [...onChangeOld, onChangeNew];
        } else if (onChangeOld) {
                fi._flatpickr.config.onChange = [onChangeOld, onChangeNew];
            } else {
                fi._flatpickr.config.onChange = onChangeNew;
            }';

    public function __construct(UtilityContext $context, bool $isInline = false, $containerSelector = '')
    {
        parent::__construct($context);
        $this->fields = [
            'containerSelector' => $containerSelector,
            'calendarSelector' => $isInline ? '.flatpickr-calendar' : '.flatpickr-calendar.inline',
            'flatpickrSelector' => '.flatpickr-input',
            'dateSet' => '.date-set',
        ];
    }

    /**
     * @param DateTime $date Date to set
     */
    public function setDate(DateTime $date, string $dateFormat = self::DATETIME_FORMAT): void
    {
        $this->context->getSession()->executeScript(sprintf(self::ADD_CALLBACK_TO_DATEPICKER_SCRIPT_FORMAT, $this->fields['containerSelector']));

        $dateScript = sprintf(self::SETTING_SCRIPT_FORMAT, $this->fields['containerSelector'], $this->fields['flatpickrSelector'], $date->format($dateFormat), $dateFormat);
        $this->context->getSession()->getDriver()->executeScript($dateScript);

        $this->context->waitUntilElementIsVisible($this->fields['dateSet']);
    }

    /**
     * @param string $hour Hour to set
     * @param string $minute Minute to set
     */
    public function setTime(string $hour, string $minute): void
    {
        $isTimeOnly = $this->context->isElementVisible('.flatpickr-calendar.noCalendar');

        if (!$isTimeOnly) {
            // get current date as it's not possible to set time without setting date
            $currentDateScript = sprintf('document.querySelector("%s %s")._flatpickr.selectedDates[0].toLocaleString()',
                $this->fields['containerSelector'],
                $this->fields['flatpickrSelector']);
            $currentDate = $this->context->getSession()->getDriver()->evaluateScript($currentDateScript);
        }

        $valueToSet = $isTimeOnly ? sprintf('%s:%s:00', $hour, $minute) : sprintf('%s, %s:%s:00', explode(',', $currentDate)[0], $hour, $minute);
        $format = $isTimeOnly ? 'H:i:S' : 'm/d/Y, H:i:S';

        $timeScript = sprintf(self::SETTING_SCRIPT_FORMAT, $this->fields['containerSelector'], $this->fields['flatpickrSelector'], $valueToSet, $format);
        $this->context->getSession()->getDriver()->executeScript($timeScript);
    }
}
