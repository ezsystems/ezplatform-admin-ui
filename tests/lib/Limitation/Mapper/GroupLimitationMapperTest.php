<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\AdminUi\Limitation\Mapper;

use eZ\Publish\API\Repository\Values\User\Limitation\UserGroupLimitation;
use Ibexa\AdminUi\Limitation\Mapper\GroupLimitationMapper;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

class GroupLimitationMapperTest extends TestCase
{
    public function testMapLimitationValue()
    {
        $expected = ['policy.limitation.group.self'];

        $translatorMock = $this->createMock(TranslatorInterface::class);
        $translatorMock
            ->expects($this->once())
            ->method('trans')
            ->willReturnArgument(0);

        $mapper = new GroupLimitationMapper($translatorMock);
        $result = $mapper->mapLimitationValue(new UserGroupLimitation([
            'limitationValues' => [1],
        ]));

        $this->assertEquals($expected, $result);
    }
}

class_alias(GroupLimitationMapperTest::class, 'EzSystems\EzPlatformAdminUi\Tests\Limitation\Mapper\GroupLimitationMapperTest');
