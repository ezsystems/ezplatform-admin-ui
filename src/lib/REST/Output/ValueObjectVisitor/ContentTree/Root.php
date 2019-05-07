<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\REST\Output\ValueObjectVisitor\ContentTree;

use EzSystems\EzPlatformRestCommon\Output\ValueObjectVisitor;
use EzSystems\EzPlatformRestCommon\Output\Generator;
use EzSystems\EzPlatformRestCommon\Output\Visitor;
use Symfony\Component\HttpFoundation\Response;

class Root extends ValueObjectVisitor
{
    /**
     * Visit struct returned by controllers.
     *
     * @param \EzSystems\EzPlatformRestCommon\Output\Visitor $visitor
     * @param \EzSystems\EzPlatformRestCommon\Output\Generator $generator
     * @param \EzSystems\EzPlatformAdminUi\REST\Value\ContentTree\Root $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data)
    {
        $generator->startObjectElement('ContentTreeRoot');
        $visitor->setHeader('Content-Type', $generator->getMediaType('ContentTreeRoot'));
        $visitor->setStatus(Response::HTTP_OK);

        $generator->startList('ContentTreeNodeList');

        foreach ($data->elements as $element) {
            $visitor->visitValueObject($element);
        }

        $generator->endList('ContentTreeNodeList');

        $generator->endObjectElement('ContentTreeRoot');
    }
}
