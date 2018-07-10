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

class Date extends EzFieldElement
{
    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'Date';

    private const DATE_FORMAT = 'm/d/Y';
    private const VIEW_DATE_FORMAT = 'n/j/y';

    public function __construct(UtilityContext $context, string $locator, string $label)
    {
        parent::__construct($context, $locator, $label);
        $this->fields['fieldInput'] = 'input.flatpickr-input.ez-data-source__input';
    }

    public function setValue(array $parameters): void
    {
        $fieldInput = $this->context->findElement(
            sprintf('%s %s', $this->fields['fieldContainer'], $this->fields['fieldInput'])
        );

        Assert::assertNotNull($fieldInput, sprintf('Input for field %s not found.', $this->label));

        $fieldInput->click();

        $dateAndTimePopup = ElementFactory::createElement($this->context, DateAndTimePopup::ELEMENT_NAME);
        $dateAndTimePopup->setDate(\DateTime::createFromFormat(self::DATE_FORMAT, $parameters['value']), self::DATE_FORMAT);
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
        $expectedDateTime = date_format(\DateTime::createFromFormat(self::DATE_FORMAT, $values['value']), self::DATE_FORMAT);
        $actualDateTime = date_format(\DateTime::createFromFormat(self::VIEW_DATE_FORMAT, $this->context->findElement($this->fields['fieldContainer'])->getText()), self::DATE_FORMAT);
        Assert::assertEquals(
            $expectedDateTime,
            $actualDateTime,
            'Field has wrong value'
        );
    }
}
