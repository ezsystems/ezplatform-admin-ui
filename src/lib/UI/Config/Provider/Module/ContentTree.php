<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\UI\Config\Provider\Module;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use EzSystems\EzPlatformAdminUi\UI\Config\ProviderInterface;

class ContentTree implements ProviderInterface
{
    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    /**
     * @param \eZ\Publish\Core\MVC\ConfigResolverInterface $configResolver
     */
    public function __construct(
        ConfigResolverInterface $configResolver
    ) {
        $this->configResolver = $configResolver;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        $rootLocationId = $this->configResolver->getParameter('content_tree_module.tree_root_location_id');

        return [
            'loadMoreLimit' => $this->configResolver->getParameter('content_tree_module.load_more_limit'),
            'childrenLoadMaxLimit' => $this->configResolver->getParameter('content_tree_module.children_load_max_limit'),
            'treeMaxDepth' => $this->configResolver->getParameter('content_tree_module.tree_max_depth'),
            'allowedContentTypes' => $this->configResolver->getParameter('content_tree_module.allowed_content_types'),
            'ignoredContentTypes' => $this->configResolver->getParameter('content_tree_module.ignored_content_types'),
            'treeRootLocationId' => $rootLocationId ?? $this->configResolver->getParameter('content.tree_root.location_id'),
            'contextualTreeRootLocationIds' => $this->configResolver->getParameter('content_tree_module.contextual_tree_root_location_ids'),
        ];
    }
}
