<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data;

final class ContentTypeUpdateV2Data
{
    /** @var \EzSystems\EzPlatformAdminUi\Form\Data\ContentTypeMetadataData */
    public $metadata;

    /** @var \EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionData[] */
    public $fieldDefinitions = [];

    /**
     * Language Code of currently edited contentTypeDraft.
     *
     * @var string|null
     */
    public $languageCode = null;

    public function __construct()
    {
        $this->metadata = new ContentTypeMetadataData();
    }
}
