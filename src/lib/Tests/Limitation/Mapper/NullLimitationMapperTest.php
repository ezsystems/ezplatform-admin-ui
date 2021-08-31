<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Tests\Limitation\Mapper;

use eZ\Publish\API\Repository\Values\User\Limitation\ContentTypeLimitation;
use EzSystems\EzPlatformAdminUi\Limitation\Mapper\NullLimitationMapper;
use PHPUnit\Framework\TestCase;

class NullLimitationMapperTest extends TestCase
{
    public function testMapLimitationValue()
    {
        $values = ['foo', 'bar', 'baz'];

        // NullLimitationMapper accepts all types of limitations
        $limitation = new ContentTypeLimitation([
            'limitationValues' => $values,
        ]);

        $mapper = new NullLimitationMapper(null);
        $result = $mapper->mapLimitationValue($limitation);

        $this->assertEquals($values, $result);
    }
}
