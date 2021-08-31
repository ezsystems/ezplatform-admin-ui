<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Translation\Extractor;

use JMS\TranslationBundle\Model\Message\XliffMessage;
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Translation\ExtractorInterface;

/**
 * Generates translation strings for limitation types.
 */
class PolicyTranslationExtractor implements ExtractorInterface
{
    const MESSAGE_DOMAIN = 'forms';
    const MESSAGE_ID_PREFIX = 'role.policy.';
    const ALL_MODULES = 'all_modules';
    const ALL_FUNCTIONS = 'all_functions';
    const ALL_MODULES_ALL_FUNCTIONS = 'all_modules_all_functions';

    /** @var array */
    private $policyMap;

    /**
     * @param array $policyMap
     */
    public function __construct(array $policyMap)
    {
        $this->policyMap = $policyMap;
    }

    /**
     * @return \JMS\TranslationBundle\Model\MessageCatalogue
     */
    public function extract(): MessageCatalogue
    {
        $catalogue = new MessageCatalogue();

        $catalogue->add($this->createMessage(self::ALL_MODULES, 'All modules'));
        $catalogue->add($this->createMessage(self::ALL_MODULES_ALL_FUNCTIONS, 'All modules / All functions'));

        foreach ($this->policyMap as $module => $functionList) {
            $catalogue->add($this->createMessage($module, $this->humanize($module)));
            $catalogue->add($this->createMessage($module . '.' . self::ALL_FUNCTIONS, $this->humanize($module) . ' / All functions'));

            foreach ($functionList as $function => $limitationList) {
                $catalogue->add($this->createMessage($module . '.' . $function, $this->humanize($module) . ' / ' . $this->humanize($function)));
            }
        }

        return $catalogue;
    }

    /**
     * @param string $id
     * @param string $desc
     *
     * @return \JMS\TranslationBundle\Model\Message\XliffMessage|null
     */
    private function createMessage(string $id, string $desc): ?XliffMessage
    {
        $id = self::MESSAGE_ID_PREFIX . $id;

        $message = new XliffMessage($id, self::MESSAGE_DOMAIN);
        $message->setNew(false);
        $message->setMeaning($desc);
        $message->setDesc($desc);
        $message->setLocaleString($desc);
        $message->addNote('key: ' . $id);

        return $message;
    }

    /**
     * Makes a technical name human readable.
     *
     * Sequences of underscores are replaced by single spaces. The first letter
     * of the resulting string is capitalized, while all other letters are
     * turned to lowercase.
     *
     * @see \Symfony\Component\Form\FormRenderer::humanize()
     *
     * @param string $text the text to humanize
     *
     * @return string the humanized text
     */
    private function humanize(string $text): string
    {
        $replace = ['Content Type'];
        $search = ['class'];

        return ucfirst(trim(str_replace($search, $replace, strtolower(preg_replace(['/([A-Z])/', '/[_\s]+/'], ['_$1', ' '], $text)))));
    }
}
