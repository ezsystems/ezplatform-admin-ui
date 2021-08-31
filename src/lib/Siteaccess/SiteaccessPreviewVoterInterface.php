<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Siteaccess;

interface SiteaccessPreviewVoterInterface
{
    /**
     * Votes whether the Content item can be previewed in given siteaccess.
     *
     * @param \EzSystems\EzPlatformAdminUi\Siteaccess\SiteaccessPreviewVoterContext $context
     *
     * @return bool
     */
    public function vote(SiteaccessPreviewVoterContext $context): bool;
}
