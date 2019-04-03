<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Config\Provider\FieldType\RichText;

use EzSystems\EzPlatformAdminUi\UI\Config\ProviderInterface;

class AlloyEditor implements ProviderInterface
{
    /** @var array */
    private $alloyEditorConfiguration;

    /**
     * @param array $alloyEditorConfiguration
     */
    public function __construct(array $alloyEditorConfiguration)
    {
        $this->alloyEditorConfiguration = $alloyEditorConfiguration;
    }

    /**
     * @return array AlloyEditor config
     */
    public function getConfig(): array
    {
        return [
            'extraPlugins' => $this->getExtraPlugins(),
            'extraButtons' => $this->getExtraButtons(),
        ];
    }

    /**
     * @return array Custom plugins
     */
    protected function getExtraPlugins(): array
    {
        return $this->alloyEditorConfiguration['extra_plugins'] ?? [];
    }

    /**
     * @deprecated 3.0.0 The alternative and more flexible solution will be introduced.
     * @deprecated 3.0.0 So you will need to update Online Editor Extra Buttons as part of eZ Platform 3.x upgrade.
     *
     * @return array Custom buttons
     */
    protected function getExtraButtons(): array
    {
        trigger_error(
            '"ezrichtext.alloy_editor.extra_buttons" is deprecated since v2.5.1. There will be new and more flexible solution to manage buttons in Online Editor in 3.0.0',
            E_USER_DEPRECATED
        );

        return $this->alloyEditorConfiguration['extra_buttons'] ?? [];
    }
}
