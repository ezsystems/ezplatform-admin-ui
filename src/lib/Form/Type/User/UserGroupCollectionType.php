<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\AdminUi\Form\Type\User;

use eZ\Publish\API\Repository\UserService;
use Ibexa\AdminUi\Form\DataTransformer\UserGroupCollectionTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

class UserGroupCollectionType extends AbstractType
{
    /** @var \eZ\Publish\API\Repository\UserService */
    protected $userService;

    /**
     * @param \eZ\Publish\API\Repository\UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer(new UserGroupCollectionTransformer($this->userService));
    }

    public function getParent(): ?string
    {
        return HiddenType::class;
    }
}

class_alias(UserGroupCollectionType::class, 'EzSystems\EzPlatformAdminUi\Form\Type\User\UserGroupCollectionType');
