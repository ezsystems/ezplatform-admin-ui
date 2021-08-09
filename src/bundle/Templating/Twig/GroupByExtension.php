<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Templating\Twig;

use EzSystems\EzPlatformAdminUiBundle\Templating\Twig\Values\Group;
use LogicException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class GroupByExtension extends AbstractExtension
{
    /**
     * @return \Twig\TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('group_by', [$this, 'groupBy']),
        ];
    }

    public function groupBy(iterable $values, callable $arrow): iterable
    {
        /** @var array<string|int,Group> $groups */
        $groups = [];

        foreach ($values as $value) {
            $key = $arrow($value);

            $hash = $this->getHashFromKey($key);
            if (!isset($groups[$hash])) {
                $groups[$hash] = new Group($key);
            }

            $groups[$hash]->entries[] = $value;
        }

        foreach ($groups as $group) {
            yield $group->key => $group->entries;
        }
    }

    /**
     * @return int|string
     */
    private function getHashFromKey($value)
    {
        if (is_object($value)) {
            return spl_object_hash($value);
        }

        if (is_string($value) || is_int($value)) {
            return $value;
        }

        throw new LogicException('Invalid key type');
    }
}
