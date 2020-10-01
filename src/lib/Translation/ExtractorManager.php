<?php

namespace EzSystems\EzPlatformAdminUi\Translation;

use JMS\TranslationBundle\Exception\InvalidArgumentException;
use JMS\TranslationBundle\Translation\Extractor\FileExtractor;
use JMS\TranslationBundle\Translation\ExtractorInterface;
use JMS\TranslationBundle\Translation\ExtractorManager as JMSExtractorManager;
use Psr\Log\LoggerInterface;

class ExtractorManager extends JMSExtractorManager implements ExtractorInterface
{
    /** @var \JMS\TranslationBundle\Translation\Extractor\FileExtractor */
    private $fileExtractor;

    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    /** @var \JMS\TranslationBundle\Translation\ExtractorInterface[] */
    private $customExtractors;

    /** @var string[] */
    private $enabledExtractors = [];

    public function __construct(
        FileExtractor $fileExtractor,
        LoggerInterface $logger,
        iterable $customExtractors = []
    ) {
        parent::__construct($fileExtractor, $logger);

        $this->fileExtractor = $fileExtractor;
        $this->logger = $logger;
        $this->customExtractors = $customExtractors;
    }

    public function setLogger(LoggerInterface $logger): void
    {
    }

    public function setEnabledExtractors(array $aliases): void
    {
        foreach ($aliases as $alias => $true) {
            if (!isset($this->customExtractors[$alias])) {
                throw new InvalidArgumentException(sprintf('There is no extractor with alias "%s". Available extractors: %s', $alias, $this->customExtractors ? implode(', ', array_keys($this->customExtractors)) : '# none #'));
            }

            $this->enabledExtractors[$alias] = $this->customExtractors[$alias];
        }
    }

    public function extract()
    {
        $jmsExtractor = new JMSExtractorManager(
            $this->fileExtractor,
            $this->logger
        );

        $catalogue = $jmsExtractor->extract();

        foreach ($this->enabledExtractors as $customExtractor) {
            $catalogue->merge(
                $customExtractor instanceof CustomExtractorInterface
                    ? $customExtractor->extract($catalogue)
                    : $customExtractor->extract()
            );
        }

        return $catalogue;
    }
}