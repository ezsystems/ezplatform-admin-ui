<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Mapper;

use eZ\Publish\API\Repository\Values\Content\Section;
use eZ\Publish\API\Repository\Values\ValueObject;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Section\SectionCreateData;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Section\SectionUpdateData;

class SectionMapper implements FormDataMapperInterface
{
    /**
     * Maps a ValueObject from eZ content repository to a data usable as underlying form data (e.g. create/update struct).
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Section|ValueObject $section
     * @param array $params
     *
     * @return SectionCreateData|SectionUpdateData
     */
    public function mapToFormData(ValueObject $section, array $params = [])
    {
        if (!$this->isSectionNew($section)) {
            $data = new SectionUpdateData(['section' => $section]);
            $data->identifier = $section->identifier;
            $data->name = $section->name;
        } else {
            $data = new SectionCreateData(['section' => $section]);
        }

        return $data;
    }

    private function isSectionNew(Section $section)
    {
        return $section->id === null;
    }
}
