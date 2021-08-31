<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Value\ObjectState;

use eZ\Publish\API\Repository\Values\ObjectState\ObjectState as APIObjectState;
use eZ\Publish\Core\Repository\Values\ObjectState\ObjectState as CoreObjectState;

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
     * @param \eZ\Publish\API\Repository\Values\ObjectState\ObjectState $objectState
     * @param array $properties
     */
    public function __construct(APIObjectState $objectState, array $properties = [])
    {
        parent::__construct(get_object_vars($objectState) + $properties);
    }
}
