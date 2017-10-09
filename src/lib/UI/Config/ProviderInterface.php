<?php
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Config;

/**
 * Provides parameters as a serializable value.
 */
interface ProviderInterface
{
    /**
     * @return mixed Anything that is serializable via json_encode()
     */
    public function getConfig();
}
