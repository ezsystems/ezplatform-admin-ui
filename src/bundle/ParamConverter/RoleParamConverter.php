<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\ParamConverter;

use eZ\Publish\API\Repository\RoleService;
use eZ\Publish\API\Repository\Values\User\Role;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RoleParamConverter implements ParamConverterInterface
{
    const PARAMETER_ROLE_ID = 'roleId';

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
        if (!$request->get(self::PARAMETER_ROLE_ID)) {
            return false;
        }

        $id = (int)$request->get(self::PARAMETER_ROLE_ID);

        $role = $this->roleService->loadRole($id);
        if (!$role) {
            throw new NotFoundHttpException("Role $id not found!");
        }

        $request->attributes->set($configuration->getName(), $role);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ParamConverter $configuration)
    {
        return Role::class === $configuration->getClass();
    }
}
