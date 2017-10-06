<?php

declare(strict_types=1);

namespace EzPlatformAdminUi\Tab;

/**
 * Tab interface representing UI tabs. Tabs are assigned to groups which are rendered in the UI.
 * Use `ezplatform.tab` tag with attribute `group` to tag your concrete implementation service.
 */
interface TabInterface
{
    /**
     * Returns identifier of the tab.
     *
     * @return string
     */
    public function getIdentifier(): string;

    /**
     * Returns name of the tab which is displayed as a tab's title in the UI.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Returns HTML body of the tab.
     *
     * @param array $parameters
     *
     * @return string
     */
    public function renderView(array $parameters): string;
}
