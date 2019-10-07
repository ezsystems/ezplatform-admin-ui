<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Language;

use eZ\Publish\API\Repository\Values\Content\LanguageCreateStruct;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\NewnessCheckable;

/**
 * @property \eZ\Publish\API\Repository\Values\Content\Language $language
 */
class LanguageCreateData extends LanguageCreateStruct implements NewnessCheckable
{
    use LanguageDataTrait;

    public function isNew(): bool
    {
        return true;
    }
}
