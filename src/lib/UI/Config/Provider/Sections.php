<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Config\Provider;

use eZ\Publish\API\Repository\SectionService;
use EzSystems\EzPlatformAdminUi\UI\Config\ProviderInterface;

/**
 * Provides information about sections.
 */
class Sections implements ProviderInterface
{
    /** @var \eZ\Publish\API\Repository\SectionService */
    private $sectionService;

    public function __construct(
        SectionService $sectionService
    ) {
        $this->sectionService = $sectionService;
    }

    public function getConfig(): array
    {
        $sections = $this->sectionService->loadSections();
        $config = [];

        /** @var \eZ\Publish\API\Repository\Values\Content\Section $section */
        foreach ($sections as $section) {
            $config[$section->identifier] = $section->name;
        }

        return $config;
    }
}
