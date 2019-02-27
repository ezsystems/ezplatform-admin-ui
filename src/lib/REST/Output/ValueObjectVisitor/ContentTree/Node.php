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

class Node extends ValueObjectVisitor
{
    /**
     * Visit struct returned by controllers.
     *
     * @param \eZ\Publish\Core\REST\Common\Output\Visitor $visitor
     * @param \eZ\Publish\Core\REST\Common\Output\Generator $generator
     * @param \EzSystems\EzPlatformAdminUi\REST\Value\ContentTree\Node $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data)
    {
        $generator->startObjectElement('ContentTreeNode');
        $visitor->setHeader('Content-Type', $generator->getMediaType('ContentTreeNode'));
        $visitor->setStatus(Response::HTTP_OK);

        $generator->startValueElement('locationId', $data->locationId);
        $generator->endValueElement('locationId');

        $generator->startValueElement('contentId', $data->contentId);
        $generator->endValueElement('contentId');

        $generator->startValueElement('name', $data->name);
        $generator->endValueElement('name');

        $generator->startValueElement('contentTypeIdentifier', $data->contentTypeIdentifier);
        $generator->endValueElement('contentTypeIdentifier');

        $generator->startValueElement('isContainer', $data->isContainer);
        $generator->endValueElement('isContainer');

        $generator->startValueElement('isInvisible', $data->isInvisible);
        $generator->endValueElement('isInvisible');

        $generator->startValueElement('displayLimit', $data->displayLimit);
        $generator->endValueElement('displayLimit');

        $generator->startValueElement('totalChildrenCount', $data->totalChildrenCount);
        $generator->endValueElement('totalChildrenCount');

        $generator->startList('children');

        foreach ($data->children as $child) {
            $visitor->visitValueObject($child);
        }

        $generator->endList('children');

        $generator->endObjectElement('ContentTreeNode');
    }
}
