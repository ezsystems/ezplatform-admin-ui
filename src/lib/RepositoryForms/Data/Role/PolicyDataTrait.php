<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Role;

use eZ\Publish\API\Repository\Values\User\PolicyDraft;

trait PolicyDataTrait
{
    /**
     * @var PolicyDraft
     */
    protected $policyDraft;

    /**
     * @var \eZ\Publish\API\Repository\Values\User\RoleDraft
     */
    protected $roleDraft;

    /**
     * Role the draft was created from.
     *
     * @var \eZ\Publish\API\Repository\Values\User\RoleDraft
     */
    protected $initialRole;

    /**
     * List of limitations that were posted.
     *
     * @var \eZ\Publish\API\Repository\Values\User\Limitation[]
     */
    protected $limitationsData;

    /**
     * Combination of module + function as a single string.
     * Example: "content|read".
     *
     * @var string
     */
    public $moduleFunction;

    public function setPolicyDraft(PolicyDraft $policyDraft)
    {
        $this->policyDraft = $policyDraft;
    }

    public function getId()
    {
        return $this->policyDraft ? $this->policyDraft->id : null;
    }

    abstract public function getModule();

    abstract public function getFunction();
}
