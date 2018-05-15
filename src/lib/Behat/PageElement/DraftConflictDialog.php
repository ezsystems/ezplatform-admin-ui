<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

use EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\DashboardTable;
use PHPUnit\Framework\Assert;

class DraftConflictDialog extends Element
{
    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'Draft Conflict Dialog';

    public $dashboardTable;

    public function __construct(UtilityContext $context)
    {
        parent::__construct($context);
        $this->fields = [
            'dialog' => '.ez-modal--version-draft-conflict.show',
            'addDraft' => '.ez-btn--add-draft',
        ];
        $this->dashboardTable = ElementFactory::createElement($context, DashboardTable::ELEMENT_NAME, $this->fields['dialog']);
    }

    public function verifyVisibility(): void
    {
        $this->context->waitUntilElementIsVisible($this->fields['dialog']);
    }

    public function createNewDraft(): void
    {
        $addDraftButton = $this->context->findElement($this->fields['addDraft']);
        Assert::assertNotNull($addDraftButton, 'Add draft button doesn\'t exist');

        $addDraftButton->click();
    }
}
