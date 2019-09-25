<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab\ContentType;

use EzSystems\EzPlatformAdminUi\Tab\AbstractEventDispatchingTab;
use EzSystems\EzPlatformAdminUi\Tab\OrderedTabInterface;

class ViewTab extends AbstractEventDispatchingTab implements OrderedTabInterface
{
    const URI_FRAGMENT = 'ez-tab-content-type-view-details';

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'view';
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        /** @Desc("View") */
        return $this->translator->trans('tab.name.view', [], 'content_type');
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return '@ezdesign/content_type/tab/view.html.twig';
    }

    /**
     * @param mixed[] $contextParameters
     *
     * @return mixed[]
     */
    public function getTemplateParameters(array $contextParameters = []): array
    {
        return $contextParameters;
    }
}
