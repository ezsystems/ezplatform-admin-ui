<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Limitation\Mapper;

use eZ\Publish\API\Repository\Values\User\Limitation;
use eZ\Publish\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use EzSystems\EzPlatformAdminUi\Limitation\LimitationValueMapperInterface;
use EzSystems\EzPlatformAdminUi\Siteaccess\SiteAccessKeyGeneratorInterface;

class SiteAccessLimitationMapper extends MultipleSelectionBasedMapper implements LimitationValueMapperInterface
{
    /** @var \eZ\Publish\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface */
    private $siteAccessService;

    /** @var \EzSystems\EzPlatformAdminUi\Siteaccess\SiteAccessKeyGeneratorInterface */
    private $siteAccessKeyGenerator;

    public function __construct(
        SiteAccessServiceInterface $siteAccessService,
        SiteAccessKeyGeneratorInterface $siteAccessKeyGenerator
    ) {
        $this->siteAccessService = $siteAccessService;
        $this->siteAccessKeyGenerator = $siteAccessKeyGenerator;
    }

    protected function getSelectionChoices()
    {
        $siteAccesses = [];
        foreach ($this->siteAccessService->getAll() as $sa) {
            $siteAccesses[$this->siteAccessKeyGenerator->generate($sa->name)] = $sa->name;
        }

        return $siteAccesses;
    }

    public function mapLimitationValue(Limitation $limitation)
    {
        $values = [];
        foreach ($this->siteAccessService->getAll() as $sa) {
            if (in_array($this->siteAccessKeyGenerator->generate($sa->name), $limitation->limitationValues)) {
                $values[] = $sa->name;
            }
        }

        return $values;
    }
}
