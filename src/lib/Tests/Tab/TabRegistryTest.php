<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Tests\Tab;

use EzSystems\EzPlatformAdminUi\Tab\AbstractTab;
use EzSystems\EzPlatformAdminUi\Tab\TabGroup;
use EzSystems\EzPlatformAdminUi\Tab\TabInterface;
use EzSystems\EzPlatformAdminUi\Tab\TabRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;

class TabRegistryTest extends TestCase
{
    private $groupName;

    public function setUp()
    {
        parent::setUp();
        $this->groupName = 'group_name';
    }

    public function testGetTabsByGroupNameWhenGroupDoesNotExist()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('Requested group named "%s" is not found. Did you forget to tag the service?', $this->groupName));

        $tabRegistry = new TabRegistry();
        $tabRegistry->getTabsByGroupName($this->groupName);
    }

    public function testGetTabsByGroupName()
    {
        $tabs = ['tab1', 'tab2'];

        $tabGroups = $this->createTabGroup($this->groupName, $tabs);

        $tabRegistry = new TabRegistry();

        $refObject = new \ReflectionObject($tabRegistry);
        $refProperty = $refObject->getProperty('tabGroups');
        $refProperty->setAccessible(true);
        $refProperty->setValue($tabRegistry, [$this->groupName => $tabGroups]);

        $this->assertSame($tabs, $tabRegistry->getTabsByGroupName($this->groupName));
    }

    public function testGetTabFromGroup()
    {
        $twig = $this->createMock(Environment::class);
        $translator = $this->createMock(TranslatorInterface::class);

        $tab1 = $this->createTab('tab1', $twig, $translator);

        $tabs = [$tab1, $this->createTab('tab2', $twig, $translator)];

        $tabRegistry = new TabRegistry();

        $tabGroups = $this->createTabGroup($this->groupName, $tabs);

        $refObject = new \ReflectionObject($tabRegistry);
        $refProperty = $refObject->getProperty('tabGroups');
        $refProperty->setAccessible(true);
        $refProperty->setValue($tabRegistry, [$this->groupName => $tabGroups]);

        $this->assertSame($tab1, $tabRegistry->getTabFromGroup('tab1', $this->groupName));
    }

    public function testGetTabFromGroupWhenGroupDoesNotExist()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('Requested group named "%s" is not found. Did you forget to tag the service?', $this->groupName));

        $tabRegistry = new TabRegistry();
        $tabRegistry->getTabFromGroup('tab1', $this->groupName);
    }

    public function testGetTabFromGroupWhenTabDoesNotExist()
    {
        $tabName = 'tab1';

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('Requested tab "%s" from group "%s" is not found. Did you forget to tag the service?', $tabName, $this->groupName));

        $tabs = [];

        $tabRegistry = new TabRegistry();

        $tabGroups = $this->createTabGroup($this->groupName, $tabs);

        $refObject = new \ReflectionObject($tabRegistry);
        $refProperty = $refObject->getProperty('tabGroups');
        $refProperty->setAccessible(true);
        $refProperty->setValue($tabRegistry, [$this->groupName => $tabGroups]);

        $tabRegistry->getTabFromGroup($tabName, $this->groupName);
    }

    public function testAddTabGroup()
    {
        $tabRegistry = new TabRegistry();

        $refObject = new \ReflectionObject($tabRegistry);
        $refProperty = $refObject->getProperty('tabGroups');
        $refProperty->setAccessible(true);
        $refProperty->setValue($tabRegistry, []);

        self::assertCount(0, $refProperty->getValue($tabRegistry));

        $tabRegistry->addTabGroup($this->createTabGroup('lorem'));
        self::assertCount(1, $refProperty->getValue($tabRegistry));

        $tabRegistry->addTabGroup($this->createTabGroup('ipsum'));
        self::assertCount(2, $refProperty->getValue($tabRegistry));
    }

    public function testAddTabGroupWithSameIdentifier()
    {
        $tabGroup = $this->createTabGroup($this->groupName);

        $tabRegistry = new TabRegistry();

        $refObject = new \ReflectionObject($tabRegistry);
        $refProperty = $refObject->getProperty('tabGroups');
        $refProperty->setAccessible(true);
        $refProperty->setValue($tabRegistry, [$this->groupName => $tabGroup]);

        self::assertCount(1, $refProperty->getValue($tabRegistry));

        $tabRegistry->addTabGroup($tabGroup);
        self::assertCount(1, $refProperty->getValue($tabRegistry));
    }

    public function testAddTabToExistingGroup()
    {
        $twig = $this->createMock(Environment::class);
        $translator = $this->createMock(TranslatorInterface::class);

        $existingTab = $this->createTab('existing_tab', $twig, $translator);
        $addedTab = $this->createTab('added_tab', $twig, $translator);

        $tabRegistry = new TabRegistry();

        $tabGroup = $this->createTabGroup($this->groupName, [$existingTab]);

        $refObject = new \ReflectionObject($tabRegistry);
        $refProperty = $refObject->getProperty('tabGroups');
        $refProperty->setAccessible(true);
        $refProperty->setValue($tabRegistry, [$this->groupName => $tabGroup]);

        $tabRegistry->addTab($addedTab, $this->groupName);

        $tabGroups = $refProperty->getValue($tabRegistry);

        $refGroup = new \ReflectionObject($tabGroups[$this->groupName]);
        $refTabs = $refGroup->getProperty('tabs');
        $refTabs->setAccessible(true);
        $tabs = $refTabs->getValue($tabGroups[$this->groupName]);

        self::assertCount(2, $tabs);
    }

    public function testAddTabToNonExistentGroup()
    {
        $newGroupName = 'new_group_name';
        $twig = $this->createMock(Environment::class);
        $translator = $this->createMock(TranslatorInterface::class);

        $addedTab = $this->createTab('added_tab', $twig, $translator);

        $tabRegistry = new TabRegistry();

        $refObject = new \ReflectionObject($tabRegistry);
        $refProperty = $refObject->getProperty('tabGroups');
        $refProperty->setAccessible(true);
        $refProperty->setValue($tabRegistry, []);

        $tabRegistry->addTab($addedTab, $newGroupName);

        $tabGroups = $refProperty->getValue($tabRegistry);

        $refGroup = new \ReflectionObject($tabGroups[$newGroupName]);
        $refTabs = $refGroup->getProperty('tabs');
        $refTabs->setAccessible(true);
        $tabs = $refTabs->getValue($tabGroups[$newGroupName]);

        self::assertCount(1, $tabs);
    }

    /**
     * Returns Tab Group.
     *
     * @param string $name
     * @param array $tabs
     *
     * @return TabGroup
     */
    private function createTabGroup(string $name = 'lorem', array $tabs = [])
    {
        return new TabGroup($name, $tabs);
    }

    /**
     * Returns Tab.
     *
     * @param string $name
     * @param Environment|MockObject $twig
     * @param TranslatorInterface|MockObject $translator
     *
     * @return TabInterface
     */
    private function createTab(string $name, Environment $twig, TranslatorInterface $translator): TabInterface
    {
        return new class($name, $twig, $translator) extends AbstractTab {
            /** @var string */
            protected $name;

            /** @var Environment */
            protected $twig;

            /** @var TranslatorInterface */
            protected $translator;

            /**
             * @param string $name
             * @param Environment $twig
             * @param TranslatorInterface $translator
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
