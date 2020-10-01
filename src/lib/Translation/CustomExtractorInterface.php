<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Translation;

use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Translation\ExtractorInterface;

interface CustomExtractorInterface extends ExtractorInterface
{
    public function extract(MessageCatalogue $currentCatalogue = null): MessageCatalogue;
}
