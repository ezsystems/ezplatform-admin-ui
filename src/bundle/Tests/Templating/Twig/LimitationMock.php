<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

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

    public function getIdentifier(): string
    {
        return $this->identifier;
    }
}
