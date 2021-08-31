<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Translation\Extractor;

use eZ\Publish\API\Repository\Values\Content\Location;
use JMS\TranslationBundle\Model\FileSource;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Translation\ExtractorInterface;

/**
 * Generates translation strings for sort options (field and order).
 */
class SortingTranslationExtractor implements ExtractorInterface
{
    private $defaultTranslations = [
        1 => 'Location path',
        2 => 'Publication date',
        3 => 'Modification date',
        4 => 'Section',
        5 => 'Location depth',
        6 => 'Content Type identifier',
        7 => 'Content Type name',
        8 => 'Location priority',
        9 => 'Content name',
    ];

    private $domain = 'content_type';

    public function extract()
    {
        $catalogue = new MessageCatalogue();
        $locationClass = new \ReflectionClass(Location::class);

        $sortConstants = array_filter(
            $locationClass->getConstants(),
            static function ($value, $key) {
                return is_scalar($value) && strtolower(substr($key, 0, 11)) === 'sort_field_';
            },
            ARRAY_FILTER_USE_BOTH
        );

        foreach ($sortConstants as $sortId) {
            if (!isset($this->defaultTranslations[$sortId])) {
                continue;
            }
            $catalogue->add(
                $this->createMessage(
                    'content_type.sort_field.' . $sortId,
                    $this->defaultTranslations[$sortId],
                    Location::class
                )
            );
        }

        $catalogue->add($this->createMessage('content_type.sort_order.0', 'Descending', Location::class));
        $catalogue->add($this->createMessage('content_type.sort_order.1', 'Ascending', Location::class));

        return $catalogue;
    }

    private function createMessage(string $id, string $desc, string $source): Message
    {
        $message = new Message\XliffMessage($id, $this->domain);
        $message->addSource(new FileSource($source));
        $message->setMeaning($desc);
        $message->setLocaleString($desc);
        $message->addNote('key: ' . $id);

        return $message;
    }
}
