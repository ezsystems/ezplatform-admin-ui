<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\RepositoryForms\Data\ContentTypeGroup;

use eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup;

trait ContentTypeGroupDataTrait
{
    /**
     * @var ContentTypeGroup
     */
    protected $contentTypeGroup;

    public function setContentTypeGroup(ContentTypeGroup $contentTypeGroup)
    {
        $this->contentTypeGroup = $contentTypeGroup;
    }

    public function getId()
    {
        return $this->contentTypeGroup ? $this->contentTypeGroup->id : null;
    }
}
