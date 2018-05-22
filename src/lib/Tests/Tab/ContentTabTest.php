<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Tests\Tab;

use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\Core\Helper\FieldsGroups\FieldsGroupsList;
use eZ\Publish\Core\REST\Client\Values\ContentType\FieldDefinition;
use EzSystems\EzPlatformAdminUi\Tab\LocationView\ContentTab;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;

class ContentTabTest extends TestCase
{
    public function testGroupFieldDefinitions()
    {
        $contentTab = $this->createForGroupFieldDefitions();

        $refl = new \ReflectionMethod(ContentTab::class, 'groupFieldDefinitions');
        $refl->setAccessible(true);

        $fieldDefinitionsByGroup = $refl->invokeArgs($contentTab, [[
            new FieldDefinition([
                'fieldGroup' => 'random', // this is a new fieldGroup which is not defined originally for the tab
            ]),
            new FieldDefinition(), // should go to the default group (content)
        ]]);

        $this->assertCount(2, $fieldDefinitionsByGroup);

        $this->assertArrayHasKey('content', $fieldDefinitionsByGroup);
        $this->assertArrayHasKey('random', $fieldDefinitionsByGroup);

        $this->assertArrayHasKey('name', $fieldDefinitionsByGroup['content']);
        $this->assertArrayHasKey('name', $fieldDefinitionsByGroup['random']);

        $this->assertSame('Content', $fieldDefinitionsByGroup['content']['name']);
        $this->assertSame('random', $fieldDefinitionsByGroup['random']['name']);

        $this->assertArrayHasKey('fieldDefinitions', $fieldDefinitionsByGroup['content']);
        $this->assertArrayHasKey('fieldDefinitions', $fieldDefinitionsByGroup['random']);

        $this->assertCount(1, $fieldDefinitionsByGroup['content']['fieldDefinitions']);
        $this->assertCount(1, $fieldDefinitionsByGroup['random']['fieldDefinitions']);
    }

    protected function createForGroupFieldDefitions()
    {
        $fieldsGroupList = $this->createMock(FieldsGroupsList::class);

        $fieldsGroupList
            ->expects(self::once())
            ->method('getGroups')
            ->willReturn([
                'content' => 'Content',
            ])
        ;
        $fieldsGroupList
            ->expects(self::once())
            ->method('getDefaultGroup')
            ->willReturn('content')
        ;

        return new ContentTab(
            $this->createMock(Environment::class),
            $this->createMock(TranslatorInterface::class),
            $fieldsGroupList,
            $this->createMock(LanguageService::class),
            []
        );
    }
}
