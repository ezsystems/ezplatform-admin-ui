<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Role;

use eZ\Publish\Core\Repository\Values\User\PolicyCreateStruct;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\NewnessCheckable;

/**
 * @property \eZ\Publish\API\Repository\Values\User\PolicyDraft $policyDraft
 * @property \eZ\Publish\API\Repository\Values\User\RoleDraft $roleDraft
 * @property \eZ\Publish\API\Repository\Values\User\Role $initialRole
 * @property \eZ\Publish\API\Repository\Values\User\Limitation[] $limitationsData
 */
class PolicyCreateData extends PolicyCreateStruct implements NewnessCheckable
{
    use PolicyDataTrait;

    public function isNew(): bool
    {
        return true;
    }

    public function getModule()
    {
        return $this->module;
    }

    public function getFunction()
    {
        return $this->function;
    }
}
