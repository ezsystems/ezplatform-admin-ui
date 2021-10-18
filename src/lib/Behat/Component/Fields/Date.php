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

class Date extends FieldTypeComponent
{
    private const DATE_FORMAT = 'm/d/Y';

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

        $this->dateAndTimePopup->setParentLocator($this->parentLocator);
        $this->dateAndTimePopup->verifyIsLoaded();
        $this->dateAndTimePopup->setDate(date_create($parameters['value']), self::DATE_FORMAT);
    }

    public function getValue(): array
    {
        $fieldSelector = CSSLocatorBuilder::base($this->parentLocator)
            ->withDescendant($this->getLocator('fieldInput'))
            ->build();

        $value = $this->getHTMLPage()->find($fieldSelector)->getText();

        return [$value];
    }

    public function verifyValueInItemView(array $values): void
    {
        $expectedDateTime = date_create($values['value']);
        $actualDateTime = date_create($this->getHTMLPage()->find($this->parentLocator)->getText());
        Assert::assertEquals(
            $expectedDateTime,
            $actualDateTime,
            'Field has wrong value'
        );
    }

    public function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('fieldInput', 'input.flatpickr-input.ibexa-data-source__input'),
        ];
    }

    public function getFieldTypeIdentifier(): string
    {
        return 'ezdate';
    }
}
