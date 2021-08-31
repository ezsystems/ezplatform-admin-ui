<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data;

use eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup;

class ContentTypeGroupData
{
    /** @var string */
    private $identifier;

    /**
     * ContentTypeGroupData constructor.
     *
     * @param string $identifier
     */
    public function __construct(string $identifier = null)
    {
        $this->identifier = $identifier;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier)
    {
        $this->identifier = $identifier;
    }

    public static function factory(ContentTypeGroup $group): self
    {
        $data = new self();
        $data->identifier = $group->identifier;

        return $data;
    }
}
