<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;
use PHPUnit\Framework\Assert;

class ContentField extends Element
{
    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'ContentField';

    public function __construct(UtilityContext $context)
    {
        parent::__construct($context);
        $this->fields = [
            'nthFieldContainer' => '.ez-content-field:nth-child(%s)',
            'fieldName' => '.ez-content-field-name',
            'fieldValue' => '.ez-content-field-value',
        ];
    }

    public function verifyFieldHasValue(string $label, string $value): void
    {
        $fieldIndex = $this->context->getElementPositionByText(sprintf('%s:', $label), $this->fields['fieldName']);
        $fieldLocator = sprintf(
            '%s %s',
            sprintf($this->fields['nthFieldContainer'], $fieldIndex + 1),
            $this->fields['fieldValue']
        );

        if ($this->context->isElementVisible($fieldLocator)) {
            $fieldDataContainer = $this->context->findElement($fieldLocator);
            Assert::assertEquals(
                $value,
                $fieldDataContainer->getText(),
                sprintf('Wrong %s value', $label)
            );
        } else {
            Assert::fail(sprintf('Field %s not found', $label));
        }
    }
}
