<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\UI\Config\Provider;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProvider;
use EzSystems\EzPlatformAdminUi\UI\Config\ProviderInterface;

class ContentTypes implements ProviderInterface
{
    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProvider */
    private $userLanguagePreferenceProvider;

    /**
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     * @param \eZ\Publish\Core\MVC\Symfony\Locale\UserLanguagePreferenceProvider $userLanguagePreferenceProvider
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
        $contentTypeGroups = [];

        $preferredLanguages = $this->userLanguagePreferenceProvider->getPreferredLanguages();
        $loadedContentTypeGroups = $this->contentTypeService->loadContentTypeGroups(
            $preferredLanguages
        );
        foreach ($loadedContentTypeGroups as $contentTypeGroup) {
            $contentTypes = $this->contentTypeService->loadContentTypes(
                $contentTypeGroup,
                $preferredLanguages
            );
            foreach ($contentTypes as $contentType) {
                $contentTypeGroups[$contentTypeGroup->identifier][] = [
                    'identifier' => $contentType->identifier,
                    'name' => $contentType->getName(),
                ];
            }
        }

        return $contentTypeGroups;
    }
}
