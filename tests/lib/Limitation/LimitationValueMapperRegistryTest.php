<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\AdminUi\Limitation;

use Ibexa\AdminUi\Exception\ValueMapperNotFoundException;
use Ibexa\AdminUi\Limitation\LimitationValueMapperInterface;
use Ibexa\AdminUi\Limitation\LimitationValueMapperRegistry;
use PHPUnit\Framework\TestCase;

class LimitationValueMapperRegistryTest extends TestCase
{
    public function testGetMappers()
    {
        $foo = $this->createMock(LimitationValueMapperInterface::class);
        $bar = $this->createMock(LimitationValueMapperInterface::class);

        $registry = new LimitationValueMapperRegistry([
            'foo' => $foo,
            'bar' => $bar,
        ]);

        $result = $registry->getMappers();

        $this->assertCount(2, $result);
        $this->assertContains($foo, $result);
        $this->assertContains($bar, $result);
    }

    public function testGetMapper()
    {
        $foo = $this->createMock(LimitationValueMapperInterface::class);

        $registry = new LimitationValueMapperRegistry([
            'foo' => $foo,
        ]);

        $this->assertEquals($foo, $registry->getMapper('foo'));
    }

    public function testGetNonExistingMapper()
    {
        $this->expectException(ValueMapperNotFoundException::class);

        $registry = new LimitationValueMapperRegistry([
            'foo' => $this->createMock(LimitationValueMapperInterface::class),
        ]);

        $registry->getMapper('bar');
    }

    public function testAddMapper()
    {
        $foo = $this->createMock(LimitationValueMapperInterface::class);

        $registry = new LimitationValueMapperRegistry();
        $registry->addMapper($foo, 'foo');

        $this->assertTrue($registry->hasMapper('foo'));
    }

    public function testHasMapper()
    {
        $registry = new LimitationValueMapperRegistry([
            'foo' => $this->createMock(LimitationValueMapperInterface::class),
        ]);

        $this->assertTrue($registry->hasMapper('foo'));
        $this->assertFalse($registry->hasMapper('bar'));
    }
}

class_alias(LimitationValueMapperRegistryTest::class, 'EzSystems\EzPlatformAdminUi\Tests\Limitation\LimitationValueMapperRegistryTest');
