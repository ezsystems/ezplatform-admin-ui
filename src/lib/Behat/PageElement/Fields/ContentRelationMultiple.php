<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields;

use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\ElementFactory;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\UniversalDiscoveryWidget;
use PHPUnit\Framework\Assert;

class ContentRelationMultiple extends EzFieldElement
{
    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'Content relations (multiple)';

    public const VIEW_PATTERN = '/Multiple relations:[\w\/,: ]* %s [\w \/,:]*/';

    public function __construct(UtilityContext $context, string $locator, string $label)
    {
        parent::__construct($context, $locator, $label);
        $this->fields['selectContent'] = '.ez-relations__cta-btn-label';
    }

    public function setValue(array $parameters): void
    {
        $selectContent = $this->context->findElement(
            sprintf('%s %s', $this->fields['fieldContainer'], $this->fields['selectContent'])
        );

        Assert::assertNotNull($selectContent, sprintf('Select content button for field %s not found.', $this->label));

        $selectContent->click();

        $UDW = ElementFactory::createElement($this->context, UniversalDiscoveryWidget::ELEMENT_NAME);
        $UDW->selectContent($parameters['firstItem']);
        $UDW->selectContent($parameters['secondItem']);
        $UDW->confirm();
    }

    public function getValue(): array
    {
        $fieldInput = $this->context->findElement(
            sprintf('%s %s', $this->fields['fieldContainer'], $this->fields['selectContent'])
        );

        Assert::assertNotNull($fieldInput, sprintf('Input for field %s not found.', $this->label));

        return [$fieldInput->getText()];
    }

    public function verifyValueInItemView(array $values): void
    {
        $explodedValue = explode('/', $values['firstItem']);
        $firstValue = $explodedValue[count($explodedValue) - 1];
        $explodedValue = explode('/', $values['secondItem']);
        $secondValue = $explodedValue[count($explodedValue) - 1];

        Assert::assertRegExp(
            sprintf(self::VIEW_PATTERN, $firstValue),
            $this->context->findElement($this->fields['fieldContainer'])->getText(),
            'Field has wrong value'
        );
        Assert::assertRegExp(
            sprintf(self::VIEW_PATTERN, $secondValue),
            $this->context->findElement($this->fields['fieldContainer'])->getText(),
            'Field has wrong value'
        );
    }
}
