<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields;

use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\DateAndTimePopup;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\ElementFactory;
use PHPUnit\Framework\Assert;

class DateAndTime extends EzFieldElement
{
    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'Date and time';

    private const DATE_FORMAT = 'm/d/Y';
    private const VIEW_DATE_FORMAT = 'd/m/Y';

    public function __construct(UtilityContext $context, string $locator, string $label)
    {
        parent::__construct($context, $locator, $label);
        $this->fields['fieldInput'] = '.flatpickr-input.ez-data-source__input';
    }

    public function setValue(array $parameters): void
    {
        $fieldInput = $this->context->findElement(
            sprintf('%s %s', $this->fields['fieldContainer'], $this->fields['fieldInput'])
        );

        Assert::assertNotNull($fieldInput, sprintf('Input for field %s not found.', $this->label));

        $fieldInput->click();

        $time = explode(':', $parameters['time']);

        $dateAndTimePopup = ElementFactory::createElement($this->context, DateAndTimePopup::ELEMENT_NAME);
        $dateAndTimePopup->setDate(\DateTime::createFromFormat($this::DATE_FORMAT, $parameters['date']));
        $dateAndTimePopup->setTime($time[0], $time[1]);

        $expectedTimeValue = date_format(date_create(sprintf('%s, %s', $parameters['date'], $parameters['time'])), 'm/d/Y, g:i:s A');

        $this->context->waitUntil($this->defaultTimeout, function () use ($expectedTimeValue) {
            return $this->context->findElement(sprintf('%s %s', $this->fields['fieldContainer'], $this->fields['fieldInput']))->getValue() === $expectedTimeValue;
        });
    }

    public function getValue(): array
    {
        $fieldInput = $this->context->findElement(
            sprintf('%s %s', $this->fields['fieldContainer'], $this->fields['fieldInput'])
        );

        Assert::assertNotNull($fieldInput, sprintf('Input for field %s not found.', $this->label));

        return [$fieldInput->getText()];
    }

    public function verifyValueInItemView(array $values): void
    {
        $expectedDate = date_format(\DateTime::createFromFormat(self::DATE_FORMAT, $values['date']), self::VIEW_DATE_FORMAT);
        Assert::assertEquals(
            sprintf('%s %s', $expectedDate, $values['time']),
            $this->context->findElement($this->fields['fieldContainer'])->getText(),
            'Field has wrong value'
        );
    }
}
