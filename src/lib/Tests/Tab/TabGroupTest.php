<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tests\Tab;

use EzSystems\EzPlatformAdminUi\Tab\TabGroup;
use EzSystems\EzPlatformAdminUi\Tab\TabInterface;
use PHPUnit\Framework\TestCase;

class TabGroupTest extends TestCase
{
    public function testAddTab()
    {
        $tabIdentifier = 'tab_identifier';

        $tab = $this->createMock(TabInterface::class);

        $tab->expects(self::once())
            ->method('getIdentifier')
            ->willReturn($tabIdentifier);

        $tabGroup = new TabGroup('group_name');

        $refGroup = new \ReflectionObject($tabGroup);
        $refTabs = $refGroup->getProperty('tabs');
        $refTabs->setAccessible(true);

        $this->assertCount(0, $refTabs->getValue($tabGroup));

        $tabGroup->addTab($tab);

        $this->assertCount(1, $refTabs->getValue($tabGroup));

        $this->assertSame($tab, $refTabs->getValue($tabGroup)[$tabIdentifier]);
    }

    public function testAddTabWithSameIdentifier()
    {
        $tabIdentifier = 'tab_identifier';

        $tab = $this->createMock(TabInterface::class);

        $tab->expects(self::once())
            ->method('getIdentifier')
            ->willReturn($tabIdentifier);

        $tabGroup = new TabGroup('group_name');

        $refGroup = new \ReflectionObject($tabGroup);
        $refTabs = $refGroup->getProperty('tabs');
        $refTabs->setAccessible(true);
        $refTabs->setValue($tabGroup, ['tab_identifier' => $tab]);

        $this->assertCount(1, $refTabs->getValue($tabGroup));

        $tabGroup->addTab($tab);

        $this->assertCount(1, $refTabs->getValue($tabGroup));

        $this->assertSame($tab, $refTabs->getValue($tabGroup)[$tabIdentifier]);
    }

    public function testRemoveTab()
    {
        $tabIdentifier = 'tab_identifier';

        $tab = $this->createMock(TabInterface::class);
        $tab->expects(self::never())->method(self::anything());

        $tabGroup = new TabGroup('group_name');

        $refGroup = new \ReflectionObject($tabGroup);
        $refTabs = $refGroup->getProperty('tabs');
        $refTabs->setAccessible(true);
        $refTabs->setValue($tabGroup, ['tab_identifier' => $tab]);

        $this->assertCount(1, $refTabs->getValue($tabGroup));

        $tabGroup->removeTab($tabIdentifier);

        $this->assertCount(0, $refTabs->getValue($tabGroup));
    }

    public function testRemoveTabWhenNotExist()
    {
        $tabIdentifier = 'tab_identifier';

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('Tab identified as "%s" is not found.', $tabIdentifier));

        $tab = $this->createMock(TabInterface::class);
        $tab->expects(self::never())->method(self::anything());

        $tabGroup = new TabGroup('group_name');

        $refGroup = new \ReflectionObject($tabGroup);
        $refTabs = $refGroup->getProperty('tabs');
        $refTabs->setAccessible(true);
        $refTabs->setValue($tabGroup, []);

        $this->assertCount(0, $refTabs->getValue($tabGroup));

        $tabGroup->removeTab($tabIdentifier);
    }
}
