<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;

/** Element that describes breadcrumb */
class Breadcrumb extends Element
{
    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'Breadcrumb';

    public function __construct(UtilityContext $context)
    {
        parent::__construct($context);
        $this->fields = [
            'breadcrumbItem' => '.breadcrumb-item',
            'breadcrumbItemLink' => '.breadcrumb-item a',
            'activeBreadcrumb' => '.breadcrumb-item.active',
        ];
    }

    public function verifyVisibility(): void
    {
        $this->context->waitUntilElementIsVisible($this->fields['breadcrumbItem']);
    }

    public function clickBreadcrumbItem(string $itemName): void
    {
        $this->context->getElementByText($itemName, $this->fields['breadcrumbItemLink'])->click();
    }

    public function getActiveName(): string
    {
        return $this->context->findElement($this->fields['activeBreadcrumb'])->getText();
    }
}
