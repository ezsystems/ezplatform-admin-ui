<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Limitation\Templating;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Values\User\Limitation;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use EzSystems\EzPlatformAdminUi\Exception\MissingLimitationBlockException;
use EzSystems\EzPlatformAdminUi\Exception\ValueMapperNotFoundException;
use EzSystems\EzPlatformAdminUi\Limitation\LimitationValueMapperRegistryInterface;
use Twig\Environment;

class LimitationBlockRenderer implements LimitationBlockRendererInterface
{
    const LIMITATION_VALUE_BLOCK_NAME = 'ez_limitation_%s_value';
    const LIMITATION_VALUE_BLOCK_NAME_FALLBACK = 'ez_limitation_value_fallback';

    /** @var \EzSystems\EzPlatformAdminUi\Limitation\LimitationValueMapperRegistryInterface */
    private $valueMapperRegistry;

    /** @var \Twig\Environment */
    private $twig;

    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    public function __construct(
        LimitationValueMapperRegistryInterface $valueMapperRegistry,
        Environment $twig,
        ConfigResolverInterface $configResolver
    ) {
        $this->valueMapperRegistry = $valueMapperRegistry;
        $this->twig = $twig;
        $this->configResolver = $configResolver;
    }

    public function renderLimitationValue(Limitation $limitation, array $parameters = [])
    {
        try {
            $blockName = $this->getValueBlockName($limitation);
            $parameters = $this->getValueBlockParameters($limitation, $parameters);
        } catch (ValueMapperNotFoundException | NotFoundException $exception) {
            $blockName = self::LIMITATION_VALUE_BLOCK_NAME_FALLBACK;
            $parameters = $this->getValueFallbackBlockParameters($limitation, $parameters);
        }

        $localTemplate = null;
        if (isset($parameters['template'])) {
            $localTemplate = $parameters['template'];
            unset($parameters['template']);
        }

        $template = $this->findTemplateWithBlock($blockName, $localTemplate);
        if ($template === null) {
            throw new MissingLimitationBlockException("Could not find a block for {$limitation->getIdentifier()}: $blockName.");
        }

        return $template->renderBlock($blockName, $parameters);
    }

    /**
     * Generates value block name based on Limitation.
     *
     * @param \eZ\Publish\API\Repository\Values\User\Limitation $limitation
     *
     * @return string
     */
    protected function getValueBlockName(Limitation $limitation)
    {
        return sprintf(self::LIMITATION_VALUE_BLOCK_NAME, strtolower($limitation->getIdentifier()));
    }

    /**
     * Find the first template containing block definition $blockName.
     *
     * @param string $blockName
     * @param string|\Twig\Template $localTemplate
     *
     * @return \Twig\TemplateWrapper|null
     */
    protected function findTemplateWithBlock($blockName, $localTemplate = null)
    {
        if ($localTemplate !== null) {
            if (is_string($localTemplate)) {
                $localTemplate = $this->twig->load($localTemplate);
            }

            if ($localTemplate->hasBlock($blockName)) {
                return $localTemplate;
            }
        }

        foreach ($this->getLimitationValueResources() as &$template) {
            if (is_string($template)) {
                // Load the template if it is necessary
                $template = $this->twig->load($template);
            }

            if ($template->hasBlock($blockName)) {
                return $template;
            }
        }

        return null;
    }

    /**
     * Get parameters passed as context of value block render.
     *
     * @param \eZ\Publish\API\Repository\Values\User\Limitation $limitation
     * @param array $parameters
     *
     * @return array
     */
    protected function getValueBlockParameters(Limitation $limitation, array $parameters)
    {
        $values = $this->valueMapperRegistry
            ->getMapper($limitation->getIdentifier())
            ->mapLimitationValue($limitation);

        $parameters += [
            'limitation' => $limitation,
            'values' => $values,
        ];

        return $parameters;
    }

    /**
     * Get parameters passed as context of value fallback block.
     *
     * @param \eZ\Publish\API\Repository\Values\User\Limitation $limitation
     * @param array $parameters
     *
     * @return array
     */
    protected function getValueFallbackBlockParameters(Limitation $limitation, array $parameters)
    {
        $parameters += [
            'limitation' => $limitation,
            'values' => $limitation->limitationValues,
        ];

        return $parameters;
    }

    private function getLimitationValueResources(): array
    {
        $resources = $this->configResolver->getParameter('limitation_value_templates');

        usort($resources, static function ($a, $b): int {
            return $b['priority'] <=> $a['priority'];
        });

        return array_column($resources, 'template');
    }
}
