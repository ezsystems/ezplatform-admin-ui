<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Role;

use eZ\Publish\Core\Repository\Values\User\PolicyUpdateStruct;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\NewnessChecker;

/**
 * @property \eZ\Publish\API\Repository\Values\User\PolicyDraft $policyDraft
 * @property \eZ\Publish\API\Repository\Values\User\RoleDraft $roleDraft
 * @property \eZ\Publish\API\Repository\Values\User\Role $initialRole
 * @property \eZ\Publish\API\Repository\Values\User\Limitation[] $limitationsData
 */
class PolicyUpdateData extends PolicyUpdateStruct
{
    use PolicyDataTrait;
    use NewnessChecker;

    protected function getIdentifierValue()
    {
        return $this->policyDraft->module;
    }

    public function getModule()
    {
        return $this->policyDraft->module;
    }

    public function getFunction()
    {
        return $this->policyDraft->function;
    }
}
