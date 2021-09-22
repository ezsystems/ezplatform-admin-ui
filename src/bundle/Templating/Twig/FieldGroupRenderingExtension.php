<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\AdminUi\Templating\Twig;

use eZ\Publish\Core\Helper\FieldsGroups\FieldsGroupsList;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class FieldGroupRenderingExtension extends AbstractExtension
{
    /** @var \eZ\Publish\Core\Helper\FieldsGroups\FieldsGroupsList */
    private $fieldsGroupsList;

    /**
     * @param \eZ\Publish\Core\Helper\FieldsGroups\FieldsGroupsList $fieldsGroupsListHelper
     */
    public function __construct(FieldsGroupsList $fieldsGroupsListHelper)
    {
        $this->fieldsGroupsList = $fieldsGroupsListHelper;
    }

    /**
     * @return \Twig\TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter(
                'ibexa_field_group_name',
                [$this, 'getFieldGroupName']
            ),
        ];
    }

    public function getFieldGroupName(?string $fieldGroupIdentifier): ?string
    {
        foreach ($this->fieldsGroupsList->getGroups() as $identifier => $name) {
            if ($fieldGroupIdentifier === $identifier) {
                return $name;
            }
        }

        return null;
    }
}
