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

class Time extends EzFieldElement
{
    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'Time';
    private const VIEW_TIME_FORMAT = 'g:i A';
    private const TIME_FORMAT = 'G:i';

    public function __construct(UtilityContext $context, string $locator, string $label)
    {
        parent::__construct($context, $locator, $label);
        $this->fields['fieldInput'] = '.ez-data-source__input-wrapper input';
    }

    public function setValue(array $parameters): void
    {
        $fieldInput = $this->context->findElement(
            sprintf('%s %s', $this->fields['fieldContainer'], $this->fields['fieldInput'])
        );

        Assert::assertNotNull($fieldInput, sprintf('Input for field %s not found.', $this->label));

        $fieldInput->click();

        $time = explode(':', $parameters['value']);

        $dateAndTimePopup = ElementFactory::createElement($this->context, DateAndTimePopup::ELEMENT_NAME);
        $dateAndTimePopup->setTime($time[0], $time[1]);

        $expectedTimeValue = $parameters['value'];

        $this->context->waitUntil($this->defaultTimeout, function () use ($expectedTimeValue) {
            return $this->context->findElement(sprintf('%s %s', $this->fields['fieldContainer'], $this->fields['fieldInput']))->getValue() === $expectedTimeValue;
        });

        // This click is closing the date and time picker, to finally ensure that value is set up.
        $this->context->findElement($this->fields['fieldContainer'])->click();
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
        $actualTime = date_format(\DateTime::createFromFormat(self::VIEW_TIME_FORMAT, $this->context->findElement($this->fields['fieldContainer'])->getText()), self::TIME_FORMAT);
        Assert::assertEquals(
            $values['value'],
            $actualTime,
            'Field has wrong value'
        );
    }
}
