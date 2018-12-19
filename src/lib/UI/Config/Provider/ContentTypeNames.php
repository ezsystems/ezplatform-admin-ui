<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\UI\Config\Provider;

use eZ\Publish\API\Repository\ContentTypeService;
use EzSystems\EzPlatformAdminUi\Translation\UserLanguagePreferenceProvider;
use EzSystems\EzPlatformAdminUi\UI\Config\ProviderInterface;

class ContentTypeNames implements ProviderInterface
{
    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \EzSystems\EzPlatformAdminUi\Translation\UserLanguagePreferenceProvider */
    private $userLanguagePreferenceProvider;

    /**
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     * @param \EzSystems\EzPlatformAdminUi\Translation\UserLanguagePreferenceProvider $userLanguagePreferenceProvider
     */
    public function __construct(ContentTypeService $contentTypeService, UserLanguagePreferenceProvider $userLanguagePreferenceProvider)
    {
        $this->contentTypeService = $contentTypeService;
        $this->userLanguagePreferenceProvider = $userLanguagePreferenceProvider;
    }

    /**
     * @return mixed Anything that is serializable via json_encode()
     */
    public function getConfig()
    {
        $contentTypeNames = [];

        $preferredLanguages = $this->userLanguagePreferenceProvider->getPreferredLanguages();
        $contentTypeGroups = $this->contentTypeService->loadContentTypeGroups($preferredLanguages);
        foreach ($contentTypeGroups as $contentTypeGroup) {
            $contentTypes = $this->contentTypeService->loadContentTypes(
                $contentTypeGroup,
                $preferredLanguages
            );
            foreach ($contentTypes as $contentType) {
                $contentTypeNames[$contentType->identifier] = $contentType->getName();
            }
        }

        return $contentTypeNames;
    }
}
