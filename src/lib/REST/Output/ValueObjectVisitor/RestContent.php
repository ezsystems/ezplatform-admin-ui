<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\REST\Output\ValueObjectVisitor;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use eZ\Publish\Core\REST\Common\Output\Generator;
use eZ\Publish\Core\REST\Common\Output\Visitor;
use eZ\Publish\Core\REST\Server\Output\ValueObjectVisitor\RestContent as BaseRestContent;
use eZ\Publish\Core\REST\Server\Values\Version as VersionValue;

/**
 * Visitor for eZ\Publish\Core\REST\Server\Values\RestContent.
 *
 * It makes sure that "Name" is loaded according to current siteaccess languages settings.
 */
final class RestContent extends BaseRestContent
{
    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    public function __construct(ContentService $contentService)
    {
        $this->contentService = $contentService;
    }

    /**
     * @param \eZ\Publish\Core\REST\Server\Values\RestContent $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $restContent = $data;
        $contentInfo = $restContent->contentInfo;
        $contentType = $restContent->contentType;
        $mainLocation = $restContent->mainLocation;
        $currentVersion = $restContent->currentVersion;

        $mediaType = ($restContent->currentVersion === null ? 'ContentInfo' : 'Content');

        $generator->startObjectElement('Content', $mediaType);

        $visitor->setHeader('Content-Type', $generator->getMediaType($mediaType));
        $visitor->setHeader('Accept-Patch', $generator->getMediaType('ContentUpdate'));

        $generator->startAttribute(
            'href',
            $data->path === null ?
                $this->router->generate('ezpublish_rest_loadContent', ['contentId' => $contentInfo->id]) :
                $data->path
        );
        $generator->endAttribute('href');

        $generator->startAttribute('remoteId', $contentInfo->remoteId);
        $generator->endAttribute('remoteId');
        $generator->startAttribute('id', $contentInfo->id);
        $generator->endAttribute('id');

        $generator->startObjectElement('ContentType');
        $generator->startAttribute(
            'href',
            $this->router->generate(
                'ezpublish_rest_loadContentType',
                ['contentTypeId' => $contentInfo->contentTypeId]
            )
        );
        $generator->endAttribute('href');
        $generator->endObjectElement('ContentType');

        // Get content name according to current SA languages settings
        if ($currentVersion instanceof VersionInfo) {
            $contentName = $currentVersion->getName();
        } else {
            $contentName = $this->contentService->loadContentByContentInfo($contentInfo)->getName();
        }

        $generator->startValueElement('Name', $contentName);
        $generator->endValueElement('Name');

        $generator->startObjectElement('Versions', 'VersionList');
        $generator->startAttribute(
            'href',
            $this->router->generate('ezpublish_rest_loadContentVersions', ['contentId' => $contentInfo->id])
        );
        $generator->endAttribute('href');
        $generator->endObjectElement('Versions');

        $generator->startObjectElement('CurrentVersion', 'Version');
        $generator->startAttribute(
            'href',
            $this->router->generate(
                'ezpublish_rest_redirectCurrentVersion',
                ['contentId' => $contentInfo->id]
            )
        );
        $generator->endAttribute('href');

        // Embed current version, if available
        if ($currentVersion !== null) {
            $visitor->visitValueObject(
                new VersionValue(
                    $currentVersion,
                    $contentType,
                    $restContent->relations
                )
            );
        }

        $generator->endObjectElement('CurrentVersion');

        $generator->startObjectElement('Section');
        $generator->startAttribute(
            'href',
            $this->router->generate('ezpublish_rest_loadSection', ['sectionId' => $contentInfo->sectionId])
        );
        $generator->endAttribute('href');
        $generator->endObjectElement('Section');

        // Main location will not exist if we're visiting the content draft
        if ($data->mainLocation !== null) {
            $generator->startObjectElement('MainLocation', 'Location');
            $generator->startAttribute(
                'href',
                $this->router->generate(
                    'ezpublish_rest_loadLocation',
                    ['locationPath' => trim($mainLocation->pathString, '/')]
                )
            );
            $generator->endAttribute('href');
            $generator->endObjectElement('MainLocation');
        }

        $generator->startObjectElement('Locations', 'LocationList');
        $generator->startAttribute(
            'href',
            $this->router->generate(
                'ezpublish_rest_loadLocationsForContent',
                ['contentId' => $contentInfo->id]
            )
        );
        $generator->endAttribute('href');
        $generator->endObjectElement('Locations');

        $generator->startObjectElement('Owner', 'User');
        $generator->startAttribute(
            'href',
            $this->router->generate('ezpublish_rest_loadUser', ['userId' => $contentInfo->ownerId])
        );
        $generator->endAttribute('href');
        $generator->endObjectElement('Owner');

        // Modification date will not exist if we're visiting the content draft
        if ($contentInfo->modificationDate !== null) {
            $generator->startValueElement(
                'lastModificationDate',
                $contentInfo->modificationDate->format('c')
            );
            $generator->endValueElement('lastModificationDate');
        }

        // Published date will not exist if we're visiting the content draft
        if ($contentInfo->publishedDate !== null) {
            $generator->startValueElement(
                'publishedDate',
                ($contentInfo->publishedDate !== null
                    ? $contentInfo->publishedDate->format('c')
                    : null)
            );
            $generator->endValueElement('publishedDate');
        }

        $generator->startValueElement(
            'mainLanguageCode',
            $contentInfo->mainLanguageCode
        );
        $generator->endValueElement('mainLanguageCode');

        $generator->startValueElement(
            'currentVersionNo',
            $contentInfo->currentVersionNo
        );
        $generator->endValueElement('currentVersionNo');

        $generator->startValueElement(
            'alwaysAvailable',
            $this->serializeBool($generator, $contentInfo->alwaysAvailable)
        );
        $generator->endValueElement('alwaysAvailable');

        $generator->startValueElement(
            'isHidden',
            $this->serializeBool($generator, $contentInfo->isHidden)
        );
        $generator->endValueElement('isHidden');

        $generator->startValueElement(
            'status',
            $this->getStatusString($contentInfo->status)
        );
        $generator->endValueElement('status');

        $generator->startObjectElement('ObjectStates', 'ContentObjectStates');
        $generator->startAttribute(
            'href',
            $this->router->generate(
                'ezpublish_rest_getObjectStatesForContent',
                ['contentId' => $contentInfo->id]
            )
        );
        $generator->endAttribute('href');
        $generator->endObjectElement('ObjectStates');

        $generator->endObjectElement('Content');
    }
}
