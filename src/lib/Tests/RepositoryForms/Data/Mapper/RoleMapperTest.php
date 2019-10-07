<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Tests\RepositoryForms\Data\Mapper;

use eZ\Publish\Core\Repository\Values\User\Role;
use eZ\Publish\Core\Repository\Values\User\RoleDraft;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Mapper\RoleMapper;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Role\RoleData;
use PHPUnit\Framework\TestCase;

class RoleMapperTest extends TestCase
{
    public function testMapToFormData()
    {
        $identifier = 'Snafu';
        $roleDraft = new RoleDraft([
            'innerRole' => new Role([
                'identifier' => $identifier,
            ]),
        ]);

        $expectedRoleData = new RoleData([
            'roleDraft' => $roleDraft,
            'identifier' => $roleDraft->identifier,
        ]);

        self::assertEquals($expectedRoleData, (new RoleMapper())->mapToFormData($roleDraft));
    }
}
