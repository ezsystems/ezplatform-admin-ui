<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Tests\EventListener;

use EzSystems\EzPlatformAdminUi\EventListener\SystemInfoTabGroupListener;
use EzSystems\EzPlatformAdminUi\Tab\SystemInfo\SystemInfoTab;
use EzSystems\EzPlatformAdminUi\Tab\TabGroup;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use PHPUnit\Framework\TestCase;
use EzSystems\EzPlatformAdminUi\Tab\Event\TabEvents;
use EzSystems\EzPlatformAdminUi\Tab\Event\TabGroupEvent;
use EzSystems\EzPlatformAdminUi\Tab\SystemInfo\TabFactory;
use EzSystems\EzPlatformAdminUi\Tab\TabRegistry;
use EzSystems\EzSupportToolsBundle\SystemInfo\SystemInfoCollectorRegistry;
use PHPUnit\Framework\MockObject\MockObject;

class SystemInfoTabGroupListenerTest extends TestCase
{
    /** @var Request */
    private $request;

    /** @var TabGroupEvent */
    private $event;

    /** @var HttpKernelInterface|MockObject */
    private $httpKernel;

    /** @var MockObject|TabRegistry */
    private $tabRegistry;

    /** @var MockObject|TabFactory */
    private $tabFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tabRegistry = $this->createMock(TabRegistry::class);
        $this->tabFactory = $this->createMock(TabFactory::class);

        $this->request = $this
            ->getMockBuilder(Request::class)
            ->setMethods(['getSession', 'hasSession'])
            ->getMock();

        $this->httpKernel = $this->createMock(HttpKernelInterface::class);
        $this->event = new TabGroupEvent();
    }

    public function testOnTabGroupPreRenderWithNoSystemInfoTabGroup()
    {
        $systemInfoCollectorRegistry = $this->createMock(SystemInfoCollectorRegistry::class);
        $systemInfoCollectorRegistry->expects(self::never())
            ->method('getIdentifiers');

        $systemInfoTabGroupListener = new SystemInfoTabGroupListener($this->tabRegistry, $this->tabFactory, $systemInfoCollectorRegistry);

        $tabGroup = new TabGroup('some_name', []);
        $this->event->setData($tabGroup);

        $systemInfoTabGroupListener->onTabGroupPreRender($this->event);
    }

    /**
     * @dataProvider dataProvider
     *
     * @param string[] $identifiers
     */
    public function testOnTabGroupPreRender($identifiers)
    {
        foreach ($identifiers as $i => $identifier) {
            $tab = $this->createMock(SystemInfoTab::class);

            $this->tabFactory
                ->expects($this->at($i))
                ->method('createTab')
                ->with($identifier)
                ->willReturn($tab);
        }

        $systemInfoCollectorRegistry = $this->createMock(SystemInfoCollectorRegistry::class);
        $systemInfoCollectorRegistry->expects(self::once())
            ->method('getIdentifiers')
            ->willReturn($identifiers);

        $systemInfoTabGroupListener = new SystemInfoTabGroupListener($this->tabRegistry, $this->tabFactory, $systemInfoCollectorRegistry);

        $tabGroup = new TabGroup('systeminfo', []);
        $this->event->setData($tabGroup);

        $systemInfoTabGroupListener->onTabGroupPreRender($this->event);
    }

    public function testSubscribedEvents()
    {
        $systemInfoCollectorRegistry = $this->createMock(SystemInfoCollectorRegistry::class);
        $systemInfoTabGroupListener = new SystemInfoTabGroupListener($this->tabRegistry, $this->tabFactory, $systemInfoCollectorRegistry);

        $this->assertSame([TabEvents::TAB_GROUP_PRE_RENDER => ['onTabGroupPreRender', 10]], $systemInfoTabGroupListener::getSubscribedEvents());
    }

    public function dataProvider(): array
    {
        return [
            'two_identifiers' => [['identifier_1', 'identifier_2']],
            'one_identifiers' => [['identifier_1']],
            'no_identifiers' => [[]],
        ];
    }
}
