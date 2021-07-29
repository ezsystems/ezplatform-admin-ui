<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tests\REST\Input\ContentType;

use EzSystems\EzPlatformAdminUi\REST\Input\Parser\ContentType\FieldDefinitionCreate;
use EzSystems\EzPlatformAdminUi\REST\Value\ContentType\FieldDefinitionCreate as FieldDefinitionCreateValue;
use EzSystems\EzPlatformRest\Input\ParsingDispatcher;
use EzSystems\EzPlatformRestBundle\Tests\Functional\TestCase;
use EzSystems\EzPlatformRest\Exceptions;

final class FieldDefinitionCreateTest extends TestCase
{
    /** @var \EzSystems\EzPlatformAdminUi\REST\Input\Parser\ContentType\FieldDefinitionCreate */
    private $parser;

    protected function setUp(): void
    {
        $this->parser = new FieldDefinitionCreate();
    }

    public function testValidInput(): void
    {
        $this->assertEquals(
            new FieldDefinitionCreateValue('ezstring', null),
            $this->parser->parse(
                [
                    'fieldTypeIdentifier' => 'ezstring',
                ],
                $this->createMock(ParsingDispatcher::class)
            )
        );

        $this->assertEquals(
            new FieldDefinitionCreateValue('ezstring', 10),
            $this->parser->parse(
                [
                    'fieldTypeIdentifier' => 'ezstring',
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
