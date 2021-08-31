<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tests\Translation\Extractor;

use EzSystems\EzPlatformAdminUi\Translation\Extractor\LimitationTranslationExtractor;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

/**
 * Test extracting translation messages for eZ Platform permission system policy map.
 */
class LimitationTranslationExtractorTest extends TestCase
{
    /**
     * Test extracting messages.
     */
    public function testExtract()
    {
        $policyMap = Yaml::parseFile(__DIR__ . '/fixtures/input_policies.yaml');

        $extractor = new LimitationTranslationExtractor($policyMap);

        $actualMessageCatalogue = $extractor->extract();

        self::assertEquals($this->getExpectedMessageCatalogue(), $actualMessageCatalogue);
    }

    /**
     * Get expected MessageCatalogue object created by the extractor.
     *
     * @return \JMS\TranslationBundle\Model\MessageCatalogue
     */
    private function getExpectedMessageCatalogue(): MessageCatalogue
    {
        $messageCatalogue = new MessageCatalogue();

        // create artificial set of messages which is aligned with what gets extracted from the input fixture
        for ($i = 1; $i <= 5; ++$i) {
            $id = "policy.limitation.identifier.limitation{$i}";
            $translated = "Limitation{$i}";

            $message = new Message\XliffMessage($id, 'ezplatform_content_forms_policies');
            $message->setNew(false);
            $message->setMeaning($translated);
            $message->setDesc($translated);
            $message->setLocaleString($translated);
            $message->addNote('key: ' . $id);

            $messageCatalogue->add($message);
        }

        return $messageCatalogue;
    }
}
