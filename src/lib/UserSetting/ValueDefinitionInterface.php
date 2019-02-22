<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UserSetting;

/**
 * Interface for displaying User Preferences in the Admin UI.
 *
 * User Preferences are not displayed by default unless
 * ValueDefinitionInterface implementation is provided.
 *
 * @deprecated since 1.5, to be removed in 2.0. Use \EzSystems\EzPlatformUser\UserSetting\ValueDefinitionInterface instead.
 */
interface ValueDefinitionInterface
{
    /**
     * Returns name of a User Preference displayed in UI.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Returns description of a User Preference displayed in UI.
     *
     * @return string
     */
    public function getDescription(): string;

    /**
     * Returns formatted value to be displayed in UI.
     *
     * @param string $storageValue
     *
     * @return string
     */
    public function getDisplayValue(string $storageValue): string;

    /**
     * Returns default value for User Preference if none is defined.
     *
     * @return string
     */
    public function getDefaultValue(): string;
}
