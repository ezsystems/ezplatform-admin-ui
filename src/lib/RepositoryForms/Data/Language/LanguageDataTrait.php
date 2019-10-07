<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Language;

use eZ\Publish\API\Repository\Values\Content\Language;

trait LanguageDataTrait
{
    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Language
     */
    protected $language;

    public function setLanguage(Language $language)
    {
        $this->language = $language;
    }

    public function getId()
    {
        return $this->language ? $this->language->id : null;
    }
}
