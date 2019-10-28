<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace EzSystems\EzPlatformAdminUiBundle\Tests\Templating\Twig;

use eZ\Publish\API\Repository\Values\User\Limitation;

class LimitationMock extends Limitation
{
    /** @var string */
    private $identifier;

    public function __construct($identifier, array $limitationValues)
    {
        parent::__construct([
            'limitationValues' => $limitationValues,
        ]);

        $this->identifier = $identifier;
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }
}
