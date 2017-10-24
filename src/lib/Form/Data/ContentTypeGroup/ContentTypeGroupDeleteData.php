<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\ContentTypeGroup;

use eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup;
use EzSystems\EzPlatformAdminUi\Form\Data\OnFailureRedirect;
use EzSystems\EzPlatformAdminUi\Form\Data\OnFailureRedirectTrait;
use EzSystems\EzPlatformAdminUi\Form\Data\OnSuccessRedirect;
use EzSystems\EzPlatformAdminUi\Form\Data\OnSuccessRedirectTrait;

class ContentTypeGroupDeleteData implements OnSuccessRedirect, OnFailureRedirect
{
    use OnSuccessRedirectTrait;
    use OnFailureRedirectTrait;

    /** @var ContentTypeGroup */
    private $contentTypeGroup;

    public function __construct(?ContentTypeGroup $contentTypeGroup = null)
    {
        $this->contentTypeGroup = $contentTypeGroup;
    }

    /**
     * @return ContentTypeGroup
     */
    public function getContentTypeGroup(): ContentTypeGroup
    {
        return $this->contentTypeGroup;
    }

    /**
     * @param ContentTypeGroup $contentTypeGroup
     */
    public function setContentTypeGroup(ContentTypeGroup $contentTypeGroup)
    {
        $this->contentTypeGroup = $contentTypeGroup;
    }
}
