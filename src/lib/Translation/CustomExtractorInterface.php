<?php

namespace EzSystems\EzPlatformAdminUi\Translation;

use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Translation\ExtractorInterface;

interface CustomExtractorInterface extends ExtractorInterface
{
    public function extract(MessageCatalogue $currentCatalogue = null): MessageCatalogue;
}