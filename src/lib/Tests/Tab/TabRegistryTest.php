<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Tests\Tab;

use EzSystems\EzPlatformAdminUi\Tab\AbstractTab;
use EzSystems\EzPlatformAdminUi\Tab\TabGroup;
use EzSystems\EzPlatformAdminUi\Tab\TabInterface;
use EzSystems\EzPlatformAdminUi\Tab\TabRegistry;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class TabRegistryTest extends TestCase
{
    private $groupName;

    protected function setUp(): void
    {
        parent::setUp();
        $this->groupName = 'group_name';
    }

    public function testGetTabsByGroupNameWhenGroupDoesNotExist()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('Could not find the requested group named "%s". Did you tag the service?', $this->groupName));

        $tabRegistry = new TabRegistry();
        $tabRegistry->getTabsByGroupName($this->groupName);
    }

    public function testGetTabsByGroupName()
    {
        $tabs = ['tab1', 'tab2'];
        $tabGroup = $this->createTabGroup($this->groupName, $tabs);
        $tabRegistry = new TabRegistry();
        $tabRegistry->addTabGroup($tabGroup);

        $this->assertSame($tabs, $tabRegistry->getTabsByGroupName($this->groupName));
    }

    public function testGetTabFromGroup()
    {
        $twig = $this->createMock(Environment::class);
        $translator = $this->createMock(TranslatorInterface::class);
        $tab1 = $this->createTab('tab1', $twig, $translator);
        $tabs = [$tab1, $this->createTab('tab2', $twig, $translator)];

        $tabRegistry = new TabRegistry();
        $tabGroup = $this->createTabGroup($this->groupName, $tabs);
        $tabRegistry->addTabGroup($tabGroup);

        $this->assertSame($tab1, $tabRegistry->getTabFromGroup('tab1', $this->groupName));
    }

    public function testGetTabFromGroupWhenGroupDoesNotExist()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('Could not find the requested group named "%s". Did you tag the service?', $this->groupName));

        $tabRegistry = new TabRegistry();
        $tabRegistry->getTabFromGroup('tab1', $this->groupName);
    }

    public function testGetTabFromGroupWhenTabDoesNotExist()
    {
        $tabName = 'tab1';

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('Could not find the requested tab "%s" from group "%s". Did you tag the service?', $tabName, $this->groupName));

        $tabs = [];
        $tabRegistry = new TabRegistry();
        $tabGroup = $this->createTabGroup($this->groupName, $tabs);
        $tabRegistry->addTabGroup($tabGroup);
        $tabRegistry->getTabFromGroup($tabName, $this->groupName);
    }

    public function testAddTabGroup()
    {
        $tabRegistry = new TabRegistry();
        $tabGroup = $this->createTabGroup();
        $tabRegistry->addTabGroup($tabGroup);

        self::assertSame($tabGroup, $tabRegistry->getTabGroup('lorem'));
    }

    public function testAddTabGroupWithSameIdentifier()
    {
        $tabGroup = $this->createTabGroup($this->groupName);
        $tabGroupWithSameIdentifier = $this->createTabGroup($this->groupName);

        $tabRegistry = new TabRegistry();
        $tabRegistry->addTabGroup($tabGroup);

        self::assertSame($tabGroup, $tabRegistry->getTabGroup($this->groupName));
        $tabRegistry->addTabGroup($tabGroupWithSameIdentifier);
        self::assertSame($tabGroupWithSameIdentifier, $tabRegistry->getTabGroup($this->groupName));
    }

    public function testAddTabToExistingGroup()
    {
        $twig = $this->createMock(Environment::class);
        $translator = $this->createMock(TranslatorInterface::class);
        $existingTab = $this->createTab('existing_tab', $twig, $translator);
        $addedTab = $this->createTab('added_tab', $twig, $translator);

        $tabRegistry = new TabRegistry();
        $tabGroup = $this->createTabGroup($this->groupName, [$existingTab]);
        $tabRegistry->addTabGroup($tabGroup);

        self::assertCount(1, $tabRegistry->getTabsByGroupName($this->groupName));
        $tabRegistry->addTab($addedTab, $this->groupName);
        self::assertCount(2, $tabRegistry->getTabsByGroupName($this->groupName));
    }

    public function testAddTabToNonExistentGroup()
    {
        $twig = $this->createMock(Environment::class);
        $translator = $this->createMock(TranslatorInterface::class);
        $addedTab = $this->createTab('added_tab', $twig, $translator);

        $tabRegistry = new TabRegistry();
        $tabRegistry->addTab($addedTab, $this->groupName);

        self::assertCount(1, $tabRegistry->getTabsByGroupName($this->groupName));
    }

    /**
     * Returns Tab Group.
     *
     * @param string $name
     * @param array $tabs
     *
     * @return \EzSystems\EzPlatformAdminUi\Tab\TabGroup
     */
    private function createTabGroup(string $name = 'lorem', array $tabs = []): TabGroup
    {
        return new TabGroup($name, $tabs);
    }

    /**
     * Returns Tab.
     *
     * @param string $name
     * @param \Twig\Environment|\PHPUnit\Framework\MockObject\MockObject $twig
     * @param \PHPUnit\Framework\MockObject\MockObject|\Symfony\Contracts\Translation\TranslatorInterface $translator
     *
     * @return \EzSystems\EzPlatformAdminUi\Tab\TabInterface
     */
    private function createTab(string $name, Environment $twig, TranslatorInterface $translator): TabInterface
    {
        return new class($name, $twig, $translator) extends AbstractTab {
            /** @var string */
            protected $name;

            /** @var \Twig\Environment */
            protected $twig;

            /** @var \Symfony\Contracts\Translation\TranslatorInterface */
            protected $translator;

            /**
             * @param string $name
             * @param \Twig\Environment $twig
             * @param \Symfony\Contracts\Translation\TranslatorInterface $translator
             */
            public function __construct(string $name = 'tab', Environment $twig, TranslatorInterface $translator)
            {
                parent::__construct($twig, $translator);
                $this->name = $name;
            }

            /**
             * Returns identifier of the tab.
             *
             * @return string
             */
            public function getIdentifier(): string
            {
                return 'identifier';
            }

            /**
             * Returns name of the tab which is displayed as a tab's title in the UI.
             *
             * @return string
             */
            public function getName(): string
            {
                return $this->name;
            }

            /**
             * Returns HTML body of the tab.
             *
             * @param array $parameters
             *
             * @return string
             */
            public function renderView(array $parameters): string
            {
                return null;
            }
        };
    }
}
