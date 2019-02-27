<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\REST\Output\ValueObjectVisitor\ContentTree;

use eZ\Publish\Core\REST\Common\Output\ValueObjectVisitor;
use eZ\Publish\Core\REST\Common\Output\Generator;
use eZ\Publish\Core\REST\Common\Output\Visitor;
use Symfony\Component\HttpFoundation\Response;

class Root extends ValueObjectVisitor
{
    /**
     * Visit struct returned by controllers.
     *
     * @param \eZ\Publish\Core\REST\Common\Output\Visitor $visitor
     * @param \eZ\Publish\Core\REST\Common\Output\Generator $generator
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
