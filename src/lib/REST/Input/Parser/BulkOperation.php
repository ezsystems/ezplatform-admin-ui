<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\REST\Input\Parser;

use EzSystems\EzPlatformAdminUi\REST\Value\BulkOperation as BulkOperationValue;
use EzSystems\EzPlatformRest\Exceptions;
use EzSystems\EzPlatformRest\Input\BaseParser;
use EzSystems\EzPlatformRest\Input\ParsingDispatcher;

class BulkOperation extends BaseParser
{
    /**
     * Parse input structure.
     *
     * @param array $data
     * @param \EzSystems\EzPlatformRest\Input\ParsingDispatcher $parsingDispatcher
     *
     * @return \EzSystems\EzPlatformAdminUi\REST\Value\BulkOperation
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher)
    {
        if (!is_array($data) || 'operations' !== key($data)) {
            throw new Exceptions\Parser('Invalid structure for BulkOperation.');
        }

        if (array_key_exists('uri', $data['operations'])) {
            $operationData[] = $data['operations'];
        } else {
            $operationData = $data['operations'];
        }

        $operations = [];
        foreach ($operationData as $operationId => $operation) {
            $operations[$operationId] = $parsingDispatcher->parse($operation, 'application/vnd.ez.api.internal.Operation');
        }

        return new BulkOperationValue($operations);
    }
}
