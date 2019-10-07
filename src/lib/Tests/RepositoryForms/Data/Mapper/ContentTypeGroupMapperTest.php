<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Tests\RepositoryForms\Data\Mapper;

use eZ\Publish\Core\Repository\Values\ContentType\ContentTypeGroup;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\ContentTypeGroup\ContentTypeGroupCreateData;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\ContentTypeGroup\ContentTypeGroupUpdateData;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Mapper\ContentTypeGroupMapper;
use PHPUnit\Framework\TestCase;

class ContentTypeGroupMapperTest extends TestCase
{
    public function testMapToCreateData()
    {
        $contentTypeGroup = new ContentTypeGroup();
        $data = (new ContentTypeGroupMapper())->mapToFormData($contentTypeGroup);
        self::assertInstanceOf(ContentTypeGroupCreateData::class, $data);
        self::assertSame($contentTypeGroup, $data->contentTypeGroup);
    }

    public function testMapToUpdateData()
    {
        $id = 123;
        $contentTypeGroup = new ContentTypeGroup(['id' => $id, 'identifier' => 'Foo']);
        $data = (new ContentTypeGroupMapper())->mapToFormData($contentTypeGroup);
        self::assertInstanceOf(ContentTypeGroupUpdateData::class, $data);
        self::assertSame($contentTypeGroup, $data->contentTypeGroup);
        self::assertSame($id, $data->getId());
    }
}
