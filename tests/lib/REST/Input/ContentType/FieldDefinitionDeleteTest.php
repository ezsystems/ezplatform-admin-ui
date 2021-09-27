<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\AdminUi\REST\Input\ContentType;

use EzSystems\EzPlatformAdminUi\REST\Value\ContentType\FieldDefinitionDelete as FieldDefinitionDeleteValue;
use EzSystems\EzPlatformRest\Exceptions;
use EzSystems\EzPlatformRest\Input\ParsingDispatcher;
use Ibexa\AdminUi\REST\Input\Parser\ContentType\FieldDefinitionDelete;
use PHPUnit\Framework\TestCase;

final class FieldDefinitionDeleteTest extends TestCase
{
    /** @var \Ibexa\AdminUi\REST\Input\Parser\ContentType\FieldDefinitionDelete */
    private $parser;

    protected function setUp(): void
    {
        $this->parser = new FieldDefinitionDelete();
    }

    public function testValidInput(): void
    {
        self::assertEquals(
            new FieldDefinitionDeleteValue(['foo', 'bar', 'baz']),
            $this->parser->parse(
                [
                    'fieldDefinitionIdentifiers' => ['foo', 'bar', 'baz'],
                ],
                $this->createMock(ParsingDispatcher::class)
            )
        );
    }

    public function testInvalidInput(): void
    {
        $this->expectException(Exceptions\Parser::class);
        $this->expectExceptionMessage("Missing or invalid 'fieldDefinitionIdentifiers' property for EzSystems\EzPlatformAdminUi\REST\Value\ContentType\FieldDefinitionDelete.");

        $this->parser->parse(
            [],
            $this->createMock(ParsingDispatcher::class)
        );
    }
}
