<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab\ContentType;

use EzSystems\EzPlatformAdminUi\Tab\AbstractTab;
use EzSystems\EzPlatformAdminUi\Tab\OrderedTabInterface;

class ViewTab extends AbstractTab implements OrderedTabInterface
{
    const URI_FRAGMENT = 'ez-tab-content-type-view-details';

    public function getIdentifier(): string
    {
        return 'view';
    }

    public function getName(): string
    {
        /** @Desc("View") */
        return $this->translator->trans('tab.name.view', [], 'content_type');
    }

    public function getOrder(): int
    {
        return 100;
    }

    public function renderView(array $parameters): string
    {
        return $this->twig->render(
            '@ezdesign/admin/content_type/tab/view.html.twig',
            [
                'content_type' => $parameters['content_type'],
                'content_type_group' => $parameters['content_type_group'],
                'field_definitions_by_group' => $parameters['field_definitions_by_group'],
                'language_code' => $parameters['language_code'],
                'can_update' => $parameters['can_update'],
                'languages' => $parameters['languages'],
            ]
        );
    }
}
