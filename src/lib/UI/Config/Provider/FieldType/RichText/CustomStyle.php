<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Config\Provider\FieldType\RichText;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use EzSystems\EzPlatformAdminUi\UI\Config\ProviderInterface;
use EzSystems\EzPlatformAdminUi\UI\Config\Mapper\FieldType\RichText\CustomStyle as CustomStyleConfigurationMapper;

/**
 * Provide information about RichText Custom Styles.
 */
class CustomStyle implements ProviderInterface
{
    /** @var ConfigResolverInterface */
    private $configResolver;

    /** @var CustomStyleConfigurationMapper */
    private $customStyleConfigurationMapper;

    /**
     * @param ConfigResolverInterface $configResolver
     * @param CustomStyleConfigurationMapper $customStyleConfigurationMapper
     */
    public function __construct(
        ConfigResolverInterface $configResolver,
        CustomStyleConfigurationMapper $customStyleConfigurationMapper
    ) {
        $this->configResolver = $configResolver;
        $this->customStyleConfigurationMapper = $customStyleConfigurationMapper;
    }

    /**
     * @return array RichText Custom Styles config
     */
    public function getConfig(): array
    {
        if ($this->configResolver->hasParameter('fieldtypes.ezrichtext.custom_styles')) {
            return $this->customStyleConfigurationMapper->mapConfig(
                $this->configResolver->getParameter('fieldtypes.ezrichtext.custom_styles')
            );
        }

        return [];
    }
}
