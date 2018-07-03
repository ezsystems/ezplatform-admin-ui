<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\Helper;


class PlatformEnvironmentVariables
{
    /** @var string[] */
    public $values;

    public function __construct()
    {
        $this->values = [
            'ROOT_CONTENT_NAME' => 'eZ Platform',
            'ROOT_CONTENT_TYPE' => 'Folder',
        ];
    }
}