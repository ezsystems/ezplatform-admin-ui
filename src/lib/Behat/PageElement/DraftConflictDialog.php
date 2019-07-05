<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

use EzSystems\Behat\Browser\Context\BrowserContext;
use EzSystems\Behat\Browser\Factory\ElementFactory;
use EzSystems\Behat\Browser\Element\Element;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\DraftConflictTable;
use PHPUnit\Framework\Assert;

class DraftConflictDialog extends Element
{
    /** @var string Name by which Element is recognised */
    public const ELEMENT_NAME = 'Draft Conflict Dialog';

    public $draftConflictTable;

    public function __construct(BrowserContext $context)
    {
        parent::__construct($context);
        $this->fields = [
            'dialog' => '.ez-modal--version-draft-conflict.show',
            'addDraft' => '.ez-btn--add-draft',
        ];
        $this->draftConflictTable = ElementFactory::createElement($context, DraftConflictTable::ELEMENT_NAME, $this->fields['dialog']);
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
