<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\REST\Input\Parser\ContentType;

use EzSystems\EzPlatformRest\Exceptions;
use EzSystems\EzPlatformRest\Input\BaseParser;
use EzSystems\EzPlatformRest\Input\ParsingDispatcher;
use EzSystems\EzPlatformAdminUi\REST\Value\ContentType\FieldDefinitionCreate as FieldDefinitionCreateValue;

final class FieldDefinitionCreate extends BaseParser
{
    public function parse(array $data, ParsingDispatcher $parsingDispatcher): FieldDefinitionCreateValue
    {
        if (!array_key_exists('fieldTypeIdentifier', $data)) {
            throw new Exceptions\Parser(
                sprintf("Missing or invalid 'fieldTypeIdentifier' property for %s.", FieldDefinitionCreateValue::class)
            );
        }

        return new FieldDefinitionCreateValue(
            $data['fieldTypeIdentifier'],
            $data['position'] ?? null
        );
    }
}
