<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Value\ObjectState;

use eZ\Publish\Core\Repository\Values\ObjectState\ObjectState as CoreObjectState;
use eZ\Publish\API\Repository\Values\ObjectState\ObjectState as APIObjectState;

/**
 * Extends original value object in order to provide additional fields.
 */
class ObjectState extends CoreObjectState
{
    /**
     * User can assign.
     *
     * @var bool
     */
    protected $userCanAssign;

    /**
     * @param APIObjectState $objectState
     * @param array $properties
     */
    public function __construct(APIObjectState $objectState, array $properties = [])
    {
        parent::__construct(get_object_vars($objectState) + $properties);
    }
}
