<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Form\Type\Role;

use eZ\Publish\API\Repository\RoleService;
use Ibexa\AdminUi\Form\DataTransformer\RoleTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

class RoleType extends AbstractType
{
    /** @var RoleService */
    protected $roleService;

    /**
     * @param RoleService $roleService
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

class_alias(RoleType::class, 'EzSystems\EzPlatformAdminUi\Form\Type\Role\RoleType');
