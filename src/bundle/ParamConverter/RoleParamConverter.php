<?php

declare(strict_types=1);

namespace EzPlatformAdminUiBundle\ParamConverter;

use eZ\Publish\API\Repository\Values\User\Role;
use EzPlatformAdminUi\Service\Role\RoleService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RoleParamConverter implements ParamConverterInterface
{
    const PARAMETER_ROLE_ID = 'roleId';
    /**
     * @var RoleService
     */
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
     * @inheritdoc
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $id = (int)$request->get(self::PARAMETER_ROLE_ID);

        $role = $this->roleService->getRole($id);
        if (!$role) {
            throw new NotFoundHttpException("Role $id not found!");
        }

        $request->attributes->set($configuration->getName(), $role);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function supports(ParamConverter $configuration)
    {
        return Role::class === $configuration->getClass();
    }
}
