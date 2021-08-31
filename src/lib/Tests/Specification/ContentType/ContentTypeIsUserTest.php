<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tests\Specification\ContentType;

use eZ\Publish\API\Repository\Values\ContentType\ContentType as APIContentType;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException;
use EzSystems\EzPlatformAdminUi\Specification\ContentType\ContentTypeIsUser;
use PHPUnit\Framework\TestCase;

class ContentTypeIsUserTest extends TestCase
{
    /**
     * @covers \EzSystems\EzPlatformAdminUi\Specification\ContentType\ContentTypeIsUser::isSatisfiedBy
     */
    public function testIsSatisfiedByInvalidArgument()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument \'$contentType\' is invalid: Must be an instance of eZ\Publish\API\Repository\Values\ContentType\ContentType');

        $specification = new ContentTypeIsUser([]);
        $specification->isSatisfiedBy(new \stdClass());
    }

    /**
     * @covers \EzSystems\EzPlatformAdminUi\Specification\ContentType\ContentTypeIsUser::isSatisfiedBy
     */
    public function testIsSatisfiedByCustomUserContentType()
    {
        $customUserContentType = 'custom_user_content_type';

        $specification = new ContentTypeIsUser([
            $customUserContentType,
        ]);

        $this->assertTrue($specification->isSatisfiedBy($this->createContentType($customUserContentType)));
    }

    /**
     * @covers \EzSystems\EzPlatformAdminUi\Specification\ContentType\ContentTypeIsUser::isSatisfiedBy
     */
    public function testIsSatisfiedByContentTypeWithEzUserField()
    {
        $specification = new ContentTypeIsUser([]);

        $contentTypeWithEzUserField = $this->createContentType(
            'ezuser', ['ezstring', 'ezuser']
        );

        $this->assertTrue($specification->isSatisfiedBy($contentTypeWithEzUserField));
    }

    /**
     * @covers \EzSystems\EzPlatformAdminUi\Specification\ContentType\ContentTypeIsUser::isSatisfiedBy
     */
    public function testIsSatisfiedByReturnFalse()
    {
        $specification = new ContentTypeIsUser([
            'content_type_a', 'content_type_b', 'content_type_c',
        ]);

        $articleContentType = $this->createContentType('article', ['ezstring', 'ezrichtext']);

        $this->assertFalse($specification->isSatisfiedBy($articleContentType));
    }

    /**
     * Creates Content Type mock with given identifier and field definitions.
     *
     * @param string $identifier
     * @param array $fieldsType
     *
     * @return \eZ\Publish\API\Repository\Values\ContentType\ContentType
     */
    private function createContentType(string $identifier, array $fieldsType = []): APIContentType
    {
        $contentType = $this->createMock(ContentType::class);
        $contentType
            ->method('__get')
            ->willReturnMap([
                ['identifier', $identifier],
            ]);

        $contentType
            ->method('hasFieldDefinitionOfType')
            ->willReturnCallback(static function (string $fieldTypeIdentifier) use ($fieldsType) {
                return in_array($fieldTypeIdentifier, $fieldsType);
            });

        return $contentType;
    }
}
