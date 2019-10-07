<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Mapper;

use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\API\Repository\Values\ValueObject;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Language\LanguageCreateData;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Language\LanguageUpdateData;

class LanguageMapper implements FormDataMapperInterface
{
    /**
     * Maps a ValueObject from eZ content repository to a data usable as underlying form data (e.g. create/update struct).
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Language|ValueObject $language
     * @param array $params
     *
     * @return LanguageCreateData|LanguageUpdateData
     */
    public function mapToFormData(ValueObject $language, array $params = [])
    {
        if (!$this->isLanguageNew($language)) {
            $data = new LanguageUpdateData(['language' => $language]);
            $data->languageCode = $language->languageCode;
            $data->name = $language->name;
            $data->enabled = $language->enabled;
        } else {
            $data = new LanguageCreateData(['language' => $language]);
        }

        return $data;
    }

    private function isLanguageNew(Language $language)
    {
        return $language->id === null;
    }
}
