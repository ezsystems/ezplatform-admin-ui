<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data;

use eZ\Publish\Core\Repository\Values\Content\ContentUpdateStruct;
use EzSystems\EzPlatformContentForms\Data\Content\FieldData;
use Symfony\Component\Validator\Constraints as Assert;

class ContentTranslationData extends ContentUpdateStruct implements NewnessCheckable
{
    /**
     * @var \EzSystems\EzPlatformContentForms\Data\Content\FieldData[]
     * @Assert\Valid()
     */
    protected $fieldsData;

    /** @var \eZ\Publish\API\Repository\Values\Content\Content */
    protected $content;

    /** @var \eZ\Publish\API\Repository\Values\ContentType\ContentType */
    protected $contentType;

    public function addFieldData(FieldData $fieldData): void
    {
        $this->fieldsData[$fieldData->fieldDefinition->identifier] = $fieldData;
    }

    public function isNew(): bool
    {
        return false;
    }
}
