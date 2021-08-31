<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\Role;

use eZ\Publish\API\Repository\RoleService;
use EzSystems\EzPlatformAdminUi\Form\DataTransformer\RoleTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

class RoleType extends AbstractType
{
    /** @var \eZ\Publish\API\Repository\RoleService */
    protected $roleService;

    /**
     * @param \eZ\Publish\API\Repository\RoleService $roleService
     */
    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer(new RoleTransformer($this->roleService));
    }

    public function getParent(): ?string
    {
        return HiddenType::class;
    }
}
