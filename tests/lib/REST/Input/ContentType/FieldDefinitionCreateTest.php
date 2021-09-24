<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\AdminUi\REST\Input\ContentType;

use EzSystems\EzPlatformAdminUi\REST\Value\ContentType\FieldDefinitionCreate as FieldDefinitionCreateValue;
use EzSystems\EzPlatformRest\Exceptions;
use EzSystems\EzPlatformRest\Input\ParsingDispatcher;
use Ibexa\AdminUi\REST\Input\Parser\ContentType\FieldDefinitionCreate;
use PHPUnit\Framework\TestCase;

final class FieldDefinitionCreateTest extends TestCase
{
    /** @var \Ibexa\AdminUi\REST\Input\Parser\ContentType\FieldDefinitionCreate */
    private $parser;

    protected function setUp(): void
    {
        $this->parser = new FieldDefinitionCreate();
    }

    public function testValidInput(): void
    {
        self::assertEquals(
            new FieldDefinitionCreateValue('ezstring', null),
            $this->parser->parse(
                [
                    'fieldTypeIdentifier' => 'ezstring',
                ],
                $this->createMock(ParsingDispatcher::class)
            )
        );

        self::assertEquals(
            new FieldDefinitionCreateValue('ezstring', 'foo_identifier', 10),
            $this->parser->parse(
                [
                    'fieldTypeIdentifier' => 'ezstring',
                    'fieldGroupIdentifier' => 'foo_identifier',
                    'position' => 10,
                ],
                $this->createMock(ParsingDispatcher::class)
            )
        );
    }

    public function testInvalidInput(): void
    {
        $this->expectException(Exceptions\Parser::class);
        $this->expectExceptionMessage("Missing or invalid 'fieldTypeIdentifier' property for EzSystems\EzPlatformAdminUi\REST\Value\ContentType\FieldDefinitionCreate.");

        $this->parser->parse(
            [],
            $this->createMock(ParsingDispatcher::class)
        );
    }
}
