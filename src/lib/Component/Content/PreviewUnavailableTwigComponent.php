<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Component\Content;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\API\Repository\Values\Content\Location;
use EzSystems\EzPlatformAdminUi\Component\Renderable;
use EzSystems\EzPlatformAdminUi\Siteaccess\NonAdminSiteaccessResolver;
use Twig\Environment;

class PreviewUnavailableTwigComponent implements Renderable
{
    /** @var Environment */
    private $twig;

    /** @var NonAdminSiteaccessResolver */
    private $siteaccessResolver;

    /**
     * @param Environment $twig
     * @param NonAdminSiteaccessResolver $siteaccessResolver
     */
    public function __construct(
        Environment $twig,
        NonAdminSiteaccessResolver $siteaccessResolver
    ) {
        $this->twig = $twig;
        $this->siteaccessResolver = $siteaccessResolver;
    }

    /**
     * @param array $parameters
     *
     * @return string
     */
    public function render(array $parameters = []): string
    {
        /** @var Location $location */
        $location = $parameters['location'];
        /** @var Content $content */
        $content = $parameters['content'];
        /** @var Language $language */
        $language = $parameters['language'];

        $siteaccesses = $this->siteaccessResolver->getSiteaccessesForLocation(
            $location,
            $content->getVersionInfo()->versionNo,
            $language->languageCode
        );

        if (empty($siteaccesses)) {
            return $this->twig->render(
                'EzPlatformAdminUiBundle:content/content_edit/component:preview_unavailable.html.twig'
            );
        }

        return '';
    }
}
