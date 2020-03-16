<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\BusinessContext;

use EzSystems\Behat\Browser\Factory\ElementFactory;
use EzSystems\Behat\Core\Environment\EnvironmentConstants;
use EzSystems\Behat\Core\Behat\ArgumentParser;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\UniversalDiscoveryWidget;

class UDWContext extends BusinessContext
{
    private $argumentParser;

    public function __construct(ArgumentParser $argumentParser)
    {
        $this->argumentParser = $argumentParser;
    }

    /**
     * @When I select content :pathToContent through UDW
     */
    public function iSelectContent(string $pathToContent): void
    {
        $udw = ElementFactory::createElement($this->browserContext, UniversalDiscoveryWidget::ELEMENT_NAME);
        $udw->verifyVisibility();
        $pathToContent = $this->argumentParser->replaceRootKeyword($pathToContent);
        $udw->selectContent($pathToContent);
    }

    /**
     * @When I select content root node through UDW
     */
    public function iSelectRootNodeContent(): void
    {
        $this->iSelectContent(EnvironmentConstants::get('ROOT_CONTENT_NAME'));
    }

    /** @When I close the UDW window */
    public function iCloseUDW(): void
    {
        ElementFactory::createElement($this->browserContext, UniversalDiscoveryWidget::ELEMENT_NAME)->cancel();
    }

    /** @When I confirm the selection in UDW */
    public function iConfirmSelection(): void
    {
        ElementFactory::createElement($this->browserContext, UniversalDiscoveryWidget::ELEMENT_NAME)->confirm();
    }
}
