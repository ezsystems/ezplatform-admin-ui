<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\ParamConverter;

use eZ\Publish\API\Repository\RoleService;
use eZ\Publish\API\Repository\Values\User\RoleAssignment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RoleAssignmentParamConverter implements ParamConverterInterface
{
    const PRAMETER_ROLE_ASSIGNMENT_ID = 'assignmentId';

    /** @var RoleService */
    private $roleService;

    /**
     * RoleParamConverter constructor.
     *
     * @param RoleService $roleService
     */
    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        if (!$request->get(self::PRAMETER_ROLE_ASSIGNMENT_ID)) {
            return false;
        }

        $roleAssigmentId = (int)$request->get(self::PRAMETER_ROLE_ASSIGNMENT_ID);

        $roleAssigment = $this->roleService->loadRoleAssignment($roleAssigmentId);
        if (!$roleAssigment) {
            throw new NotFoundHttpException("Role assignment $roleAssigmentId not found!");
        }

        $request->attributes->set($configuration->getName(), $roleAssigment);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ParamConverter $configuration)
    {
        return RoleAssignment::class === $configuration->getClass();
    }
}
