<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\ParamConverter;

use eZ\Publish\API\Repository\RoleService;
use eZ\Publish\API\Repository\Values\User\PolicyDraft;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PolicyDraftParamConverter implements ParamConverterInterface
{
    const PARAMETER_ROLE_ID = 'roleId';
    const PARAMETER_POLICY_ID = 'policyId';

    /** @var \eZ\Publish\API\Repository\RoleService */
    private $roleService;

    /**
     * RoleParamConverter constructor.
     *
     * @param \eZ\Publish\API\Repository\RoleService $roleService
     */
    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * @inheritdoc
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        if (!$request->get(self::PARAMETER_ROLE_ID) || !$request->get(self::PARAMETER_POLICY_ID)) {
            return false;
        }

        $roleId = (int)$request->get(self::PARAMETER_ROLE_ID);

        $roleDraft = $this->roleService->loadRoleDraftByRoleId($roleId);

        if (!$roleDraft) {
            throw new NotFoundHttpException("Role $roleId not found.");
        }

        $policyId = (int)$request->get(self::PARAMETER_POLICY_ID);

        $policyDraft = null;
        foreach ($roleDraft->getPolicies() as $item) {
            if ($item->originalId === $policyId) {
                $policyDraft = $item;
                break;
            }
        }

        if (!$policyDraft) {
            throw new NotFoundHttpException("Policy draft $policyId not found.");
        }

        $request->attributes->set($configuration->getName(), $policyDraft);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function supports(ParamConverter $configuration)
    {
        return PolicyDraft::class === $configuration->getClass();
    }
}
