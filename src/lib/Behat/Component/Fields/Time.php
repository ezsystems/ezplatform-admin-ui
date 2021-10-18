<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\Component\Fields;

use Behat\Mink\Session;
use Ibexa\AdminUi\Behat\Component\DateAndTimePopup;
use Ibexa\Behat\Browser\Locator\CSSLocatorBuilder;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use PHPUnit\Framework\Assert;

class Time extends FieldTypeComponent
{
    private const VALUE_TIME_FORMAT = 'G:i';

    /** @var \Ibexa\AdminUi\Behat\Component\DateAndTimePopup */
    private $dateAndTimePopup;

    public function __construct(Session $session, DateAndTimePopup $dateAndTimePopup)
    {
        parent::__construct($session);
        $this->dateAndTimePopup = $dateAndTimePopup;
    }

    public function setValue(array $parameters): void
    {
        $fieldSelector = CSSLocatorBuilder::base($this->parentLocator)
            ->withDescendant($this->getLocator('fieldInput'))
            ->build();

        $this->getHTMLPage()->find($fieldSelector)->click();

        $time = explode(':', $parameters['value']);

        $this->dateAndTimePopup->setParentLocator($this->parentLocator);
        $this->dateAndTimePopup->verifyIsLoaded();
        $this->dateAndTimePopup->setTime((int)$time[0], (int)$time[1]);

        // This click is closing the date and time picker, to finally ensure that value is set up.
        $this->getHTMLPage()->find($this->parentLocator)->click();

        $expectedTimeValue = date_format(date_create($parameters['value']), self::VALUE_TIME_FORMAT);
        $actualTimeValue = date_format(date_create($this->getHTMLPage()->find($fieldSelector)->getValue()), self::VALUE_TIME_FORMAT);

        Assert::assertEquals($expectedTimeValue, $actualTimeValue);
    }

    public function verifyValueInItemView(array $values): void
    {
        $actualTimeValue = date_format(date_create($this->getHTMLPage()->find($this->parentLocator)->getText()), self::VALUE_TIME_FORMAT);
        $expectedTimeValue = date_format(date_create($values['value']), self::VALUE_TIME_FORMAT);
        Assert::assertEquals(
            $expectedTimeValue,
            $actualTimeValue,
            'Field has wrong value'
        );
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('fieldInput', '.ibexa-data-source__input-wrapper input'),
        ];
    }

    public function getFieldTypeIdentifier(): string
    {
        return 'eztime';
    }
}
