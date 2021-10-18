<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Behat\Component;

use Behat\Mink\Session;
use Ibexa\AdminUi\Behat\Component\Table\TableBuilder;
use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

class DraftConflictDialog extends Component
{
    /** @var \Ibexa\AdminUi\Behat\Component\Table\Table */
    private $table;

    public function __construct(Session $session, TableBuilder $tableBuilder)
    {
        parent::__construct($session);
        $this->table = $tableBuilder->newTable()->withParentLocator($this->getLocator('table'))->build();
    }

    public function createNewDraft(): void
    {
        $this->getHTMLPage()->find($this->getLocator('addDraft'))->click();
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->setTimeout(5)->find($this->getLocator('dialog'))->assert()->isVisible();
    }

    public function edit(string $versionNumber): void
    {
        $this->table->getTableRow(['Version' => $versionNumber])->edit();
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('dialog', '#version-draft-conflict-modal.ez-modal--version-draft-conflict.show .modal-content'),
            new VisibleCSSLocator('addDraft', '.ibexa-btn--add-draft'),
            new VisibleCSSLocator('table', '#version-draft-conflict-modal .modal-content'),
        ];
    }
}
