<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Config\Provider\FieldType\RichText;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use EzSystems\EzPlatformAdminUi\UI\Config\ProviderInterface;
use EzSystems\EzPlatformAdminUi\UI\Config\Mapper\FieldType\RichText\CustomTag as CustomTagConfigurationMapper;

/**
 * Provide information about RichText Custom Tags.
 */
class CustomTag implements ProviderInterface
{
    /** @var ConfigResolverInterface */
    private $configResolver;

    /** @var CustomTagConfigurationMapper */
    private $customTagConfigurationMapper;

    /**
     * @param ConfigResolverInterface $configResolver
     * @param CustomTagConfigurationMapper $customTagConfigurationMapper
     */
    public function __construct(
        ConfigResolverInterface $configResolver,
        CustomTagConfigurationMapper $customTagConfigurationMapper
    ) {
        $this->configResolver = $configResolver;
        $this->customTagConfigurationMapper = $customTagConfigurationMapper;
    }

    /**
     * @return array RichText Custom Tags config
     */
    public function getConfig(): array
    {
        if ($this->configResolver->hasParameter('fieldtypes.ezrichtext.custom_tags')) {
            return $this->customTagConfigurationMapper->mapConfig(
                $this->configResolver->getParameter('fieldtypes.ezrichtext.custom_tags')
            );
        }

        return [];
    }
}
