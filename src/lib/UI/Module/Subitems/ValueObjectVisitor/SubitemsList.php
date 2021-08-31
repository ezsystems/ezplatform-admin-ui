<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Module\Subitems\ValueObjectVisitor;

use EzSystems\EzPlatformRest\Output\Generator;
use EzSystems\EzPlatformRest\Output\ValueObjectVisitor;
use EzSystems\EzPlatformRest\Output\Visitor;

class SubitemsList extends ValueObjectVisitor
{
    /**
     * @param \EzSystems\EzPlatformRest\Output\Visitor $visitor
     * @param \EzSystems\EzPlatformRest\Output\Generator $generator
     * @param \EzSystems\EzPlatformAdminUi\UI\Module\Subitems\Values\SubitemsList $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data)
    {
        $generator->startObjectElement('SubitemsList');
        $visitor->setHeader('Content-Type', $generator->getMediaType('SubitemsList'));
        //@todo Needs refactoring, disabling certain headers should not be done this way
        $visitor->setHeader('Accept-Patch', false);

        $generator->startList('SubitemsRow');
        foreach ($data->subitemRows as $subitemsRow) {
            $visitor->visitValueObject($subitemsRow);
        }
        $generator->endList('SubitemsRow');

        $generator->startValueElement('ChildrenCount', $data->childrenCount);
        $generator->endValueElement('ChildrenCount');

        $generator->endObjectElement('SubitemsList');
    }
}
