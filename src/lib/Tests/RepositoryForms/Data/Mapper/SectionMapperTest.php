<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Tests\RepositoryForms\Data\Mapper;

use eZ\Publish\API\Repository\Values\Content\Section;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Mapper\SectionMapper;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Section\SectionCreateData;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Section\SectionUpdateData;
use PHPUnit\Framework\TestCase;

class SectionMapperTest extends TestCase
{
    public function testMapToSectionCreateData()
    {
        $section = new Section();
        $sectionData = (new SectionMapper())->mapToFormData($section);
        self::assertInstanceOf(SectionCreateData::class, $sectionData);
        self::assertSame($section, $sectionData->section);
    }

    public function testMapToSectionUpdateData()
    {
        $sectionId = 123;
        $section = new Section(['id' => $sectionId, 'name' => 'Foo', 'identifier' => 'foo']);
        $sectionData = (new SectionMapper())->mapToFormData($section);
        self::assertInstanceOf(SectionUpdateData::class, $sectionData);
        self::assertSame($section, $sectionData->section);
        self::assertSame($sectionId, $sectionData->getId());
    }
}
