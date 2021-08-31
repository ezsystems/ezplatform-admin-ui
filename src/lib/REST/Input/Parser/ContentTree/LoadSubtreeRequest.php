<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\REST\Input\Parser\ContentTree;

use EzSystems\EzPlatformAdminUi\REST\Value\ContentTree\LoadSubtreeRequest as LoadSubtreeRequestValue;
use EzSystems\EzPlatformRest\Exceptions;
use EzSystems\EzPlatformRest\Input\BaseParser;
use EzSystems\EzPlatformRest\Input\ParsingDispatcher;

class LoadSubtreeRequest extends BaseParser
{
    /**
     * @inheritdoc
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher): LoadSubtreeRequestValue
    {
        if (!array_key_exists('nodes', $data) || !is_array($data['nodes'])) {
            throw new Exceptions\Parser(
                sprintf("Missing or invalid 'nodes' property for %s.", self::class)
            );
        }

        $nodes = [];
        foreach ($data['nodes'] as $node) {
            $nodes[] = $parsingDispatcher->parse($node, $node['_media-type']);
        }

        return new LoadSubtreeRequestValue($nodes);
    }
}
