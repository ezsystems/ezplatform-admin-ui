<?php

namespace EzSystems\EzPlatformAdminUi\Translation;

use JMS\TranslationBundle\Exception\InvalidArgumentException;
use JMS\TranslationBundle\Logger\LoggerAwareInterface;
use JMS\TranslationBundle\Model\MessageCatalogue;
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

    /** @var \JMS\TranslationBundle\Translation\ExtractorInterface[] */
    private $enabledExtractors = [];

    /** @var string[] */
    private $directories = [];

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
        $this->logger = $logger;
        $this->fileExtractor->setLogger($logger);

        foreach ($this->customExtractors as $extractor) {
            if (!$extractor instanceof LoggerAwareInterface) {
                continue;
            }

            $extractor->setLogger($logger);
        }
    }

    public function setDirectories(array $directories): void
    {
        $this->directories = [];

        foreach ($directories as $dir) {
            $this->addDirectory($dir);
        }
    }

    public function addDirectory($directory): void
    {
        if (!is_dir($directory)) {
            throw new InvalidArgumentException(sprintf('The directory "%s" does not exist.', $directory));
        }

        $this->directories[] = $directory;
    }

    public function setExcludedDirs(array $dirs): void
    {
        $this->fileExtractor->setExcludedDirs($dirs);
    }

    public function setExcludedNames(array $names): void
    {
        $this->fileExtractor->setExcludedNames($names);
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
        $catalogue = new MessageCatalogue();

        foreach ($this->directories as $directory) {
            $this->logger->info(sprintf('Extracting messages from directory : %s', $directory));
            $this->fileExtractor->setDirectory($directory);
            $catalogue->merge($this->fileExtractor->extract());
        }

        foreach ($this->enabledExtractors as $alias => $customExtractor) {
            $this->logger->info(sprintf('Extracting messages with custom extractor : %s', $alias));

            $catalogue->merge(
                $customExtractor instanceof CustomExtractorInterface
                    ? $customExtractor->extract($catalogue)
                    : $customExtractor->extract()
            );
        }

        return $catalogue;
    }
}