<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Tests\Limitation\Mapper;

use eZ\Publish\API\Repository\Values\User\Limitation\ParentDepthLimitation;
use EzSystems\EzPlatformAdminUi\Limitation\Mapper\ParentDepthLimitationMapper;
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
