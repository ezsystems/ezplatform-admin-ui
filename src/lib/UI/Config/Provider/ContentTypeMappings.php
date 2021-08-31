<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\UI\Config\Provider;

use EzSystems\EzPlatformAdminUi\UI\Config\ProviderInterface;

/**
 * Class responsible for generating PlatformUI configuration for Multi File Upload functionality.
 */
class ContentTypeMappings implements ProviderInterface
{
    /** @var array */
    protected $locationMappings = [];

    /** @var array */
    protected $defaultMappings = [];

    /** @var array */
    protected $fallbackContentType = [];

    /** @var int */
    protected $maxFileSize = 0;

    /**
     * @param array $locationMappings
     * @param array $defaultMappings
     * @param array $fallbackContentType
     * @param int $maxFileSize
     */
    public function __construct(
        array $locationMappings,
        array $defaultMappings,
        array $fallbackContentType,
        $maxFileSize
    ) {
        $this->locationMappings = $locationMappings;
        $this->defaultMappings = $defaultMappings;
        $this->fallbackContentType = $fallbackContentType;
        $this->maxFileSize = $maxFileSize;
    }

    /**
     * Returns configuration structure compatible with PlatformUI.
     *
     * @return array
     */
    public function getConfig(): array
    {
        $structure = [
            'locationMappings' => [],
            'defaultMappings' => [],
            'fallbackContentType' => $this->buildFallbackContentTypeStructure($this->fallbackContentType),
            'maxFileSize' => $this->maxFileSize,
        ];

        foreach ($this->locationMappings as $locationIdentifier => $locationConfiguration) {
            $structure['locationMappings'][$locationIdentifier] = [
                'contentTypeIdentifier' => $locationConfiguration['content_type_identifier'],
                'mimeTypeFilter' => $locationConfiguration['mime_type_filter'],
                'mappings' => [],
            ];

            foreach ($locationConfiguration['mappings'] as $mappingGroup) {
                $structure['locationMappings'][$locationIdentifier]['mappings'][] = $this->buildMappingGroupStructure($mappingGroup);
            }
        }

        foreach ($this->defaultMappings as $mappingGroup) {
            $structure['defaultMappings'][] = $this->buildMappingGroupStructure($mappingGroup);
        }

        return $structure;
    }

    /**
     * @param array $mappingGroup
     *
     * @return array
     */
    private function buildMappingGroupStructure(array $mappingGroup)
    {
        return [
            'mimeTypes' => $mappingGroup['mime_types'],
            'contentTypeIdentifier' => $mappingGroup['content_type_identifier'],
            'contentFieldIdentifier' => $mappingGroup['content_field_identifier'],
            'nameFieldIdentifier' => $mappingGroup['name_field_identifier'],
        ];
    }

    /**
     * @param array $fallbackContentType
     *
     * @return array
     */
    private function buildFallbackContentTypeStructure(array $fallbackContentType)
    {
        return [
            'contentTypeIdentifier' => $fallbackContentType['content_type_identifier'],
            'contentFieldIdentifier' => $fallbackContentType['content_field_identifier'],
            'nameFieldIdentifier' => $fallbackContentType['name_field_identifier'],
        ];
    }
}
