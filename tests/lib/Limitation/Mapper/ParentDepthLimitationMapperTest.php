<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\AdminUi\Limitation\Mapper;

use eZ\Publish\API\Repository\Values\User\Limitation\ParentDepthLimitation;
use Ibexa\AdminUi\Limitation\Mapper\ParentDepthLimitationMapper;
use PHPUnit\Framework\TestCase;

class ParentDepthLimitationMapperTest extends TestCase
{
    public function testMapLimitationValue()
    {
        $mapper = new ParentDepthLimitationMapper(1024);
        $result = $mapper->mapLimitationValue(new ParentDepthLimitation([
            'limitationValues' => [256],
        ]));

        $this->assertEquals([256], $result);
    }
}

class_alias(ParentDepthLimitationMapperTest::class, 'EzSystems\EzPlatformAdminUi\Tests\Limitation\Mapper\ParentDepthLimitationMapperTest');
