<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\REST\Input\Parser\ContentTree;

use eZ\Publish\Core\REST\Common\Exceptions;
use eZ\Publish\Core\REST\Common\Input\BaseParser;
use eZ\Publish\Core\REST\Common\Input\ParsingDispatcher;
use EzSystems\EzPlatformAdminUi\REST\Value\ContentTree\LoadSubtreeRequestNode as LoadSubtreeRequestNodeValue;

class LoadSubtreeRequestNode extends BaseParser
{
    /**
     * {@inheritdoc}
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher): LoadSubtreeRequestNodeValue
    {
        if (!array_key_exists('locationId', $data) || !is_numeric($data['locationId'])) {
            throw new Exceptions\Parser(sprintf("Missing or invalid 'locationId' property for %s.", self::class));
        }

        if (!array_key_exists('limit', $data) || !is_numeric($data['limit'])) {
            throw new Exceptions\Parser(sprintf("Missing or invalid 'limit' property for %s.",
                self::class));
        }

        if (!array_key_exists('offset', $data) || !is_numeric($data['offset'])) {
            throw new Exceptions\Parser(sprintf("Missing or invalid 'offset' property for %s.",
                self::class));
        }

        if (!array_key_exists('children', $data) || !is_array($data['children'])) {
            throw new Exceptions\Parser(sprintf("Missing or invalid 'children' property for %s.",
                self::class));
        }

        $children = array_map(
            function (array $child) use ($parsingDispatcher): LoadSubtreeRequestNodeValue {
                return $parsingDispatcher->parse($child, $child['_media-type']);
            },
            $data['children']
        );

        return new LoadSubtreeRequestNodeValue(
            (int) $data['locationId'],
            (int) $data['limit'],
            (int) $data['offset'],
            $children
        );
    }
}
