<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UniversalDiscovery;

use eZ\Publish\API\Repository\Values\ValueObject;

final class CotfCreateRestrictions extends ValueObject
{
    /** @var bool */
    protected $hasAccess;

    /** @var array */
    protected $restrictedContentTypesIds;

    /** @var array */
    protected $restrictedLanguagesCodes;

    /**
     * @param bool $hasAccess
     * @param array $restrictedContentTypesIds
     * @param array $restrictedLanguagesCodes
     */
    public function __construct(bool $hasAccess, array $restrictedContentTypesIds = [], array $restrictedLanguagesCodes = [])
    {
        parent::__construct();

        $this->hasAccess = $hasAccess;
        $this->restrictedContentTypesIds = $restrictedContentTypesIds;
        $this->restrictedLanguagesCodes = $restrictedLanguagesCodes;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'hasAccess' => $this->hasAccess,
            'restrictedContentTypesIds' => $this->restrictedContentTypesIds,
            'restrictedLanguagesCodes' => $this->restrictedLanguagesCodes,
        ];
    }
}
