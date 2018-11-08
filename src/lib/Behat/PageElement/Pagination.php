<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;

class Pagination extends Element
{
    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'Pagination';

    public function __construct(UtilityContext $context)
    {
        parent::__construct($context);
        $this->fields = [
            'nextButton' => '.pagination .page-item.next',
            'spinner' => '.m-sub-items__spinner-wrapper',
        ];
    }

    public function isNextButtonActive(): bool
    {
        return $this->context->isElementVisible($this->fields['nextButton']);
    }

    public function clickNextButton(): void
    {
        $this->context->findElement($this->fields['nextButton'])->click();
        $this->context->waitUntilElementDisappears($this->fields['spinner'], 5);
    }
}
