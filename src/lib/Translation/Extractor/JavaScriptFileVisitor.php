<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Translation\Extractor;

use Doctrine\Common\Annotations\DocParser;
use JMS\TranslationBundle\Annotation\Desc;
use JMS\TranslationBundle\Logger\LoggerAwareInterface;
use JMS\TranslationBundle\Model\FileSource;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Translation\Extractor\FileVisitorInterface;
use Peast\Peast;
use Peast\Syntax\Exception;
use Peast\Syntax\Node;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use SplFileInfo;
use Twig\Node\Node as TwigNode;

class JavaScriptFileVisitor implements FileVisitorInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    const TRANSLATOR_OBJECT = 'Translator';
    const TRANSLATOR_METHOD = 'trans';

    const ID_ARG = 0;
    const DOMAIN_ARG = 2;

    /** @var \Doctrine\Common\Annotations\DocParser */
    private $docParser;

    /** @var string */
    private $defaultDomain;

    /**
     * JavaScriptFileVisitor constructor.
     *
     * @param string $defaultDomain
     */
    public function __construct(string $defaultDomain = 'messages')
    {
        $this->logger = new NullLogger();
        $this->defaultDomain = $defaultDomain;

        $this->docParser = new DocParser();
        $this->docParser->setIgnoreNotImportedAnnotations(true);
        $this->docParser->setImports([
            'desc' => Desc::class,
        ]);
    }

    public function visitFile(SplFileInfo $file, MessageCatalogue $catalogue)
    {
        if (!$this->supports($file)) {
            return;
        }

        try {
            $source = file_get_contents($file->getRealPath());

            $parser = Peast::latest($source, [
                'comments' => true,
                'jsx' => true,
                'sourceType' => Peast::SOURCE_TYPE_MODULE,
            ]);

            $ast = $parser->parse();
        } catch (Exception $e) {
            $this->logger->error(sprintf(
                'Unable to parse file %s: %s in line %d column %d',
                $file->getRealPath(),
                $e->getMessage(),
                $e->getPosition()->getLine(),
                $e->getPosition()->getColumn()
            ));

            return;
        }

        $ast->traverse(function ($node) use ($catalogue, $file) {
            if ($this->isMethodCall($node, self::TRANSLATOR_OBJECT, self::TRANSLATOR_METHOD)) {
                $arguments = $node->getArguments();

                $id = $this->extractId($file, $arguments);
                if ($id !== null) {
                    $message = new Message($id, $this->extractDomain($file, $arguments) ?? $this->defaultDomain);
                    $message->setDesc($this->extractDesc($arguments));
                    $message->addSource(new FileSource((string)$file));

                    $catalogue->add($message);
                }
            }
        });
    }

    public function visitPhpFile(SplFileInfo $file, MessageCatalogue $catalogue, array $ast)
    {
    }

    public function visitTwigFile(SplFileInfo $file, MessageCatalogue $catalogue, TwigNode $ast)
    {
    }

    /**
     * Returns true if node is a method call.
     *
     * @param \Peast\Syntax\Node\Node $node
     * @param string $objectName
     * @param string $methodName
     *
     * @return bool
     */
    private function isMethodCall(Node\Node $node, string $objectName, string $methodName): bool
    {
        if ($node instanceof Node\CallExpression) {
            $callee = $node->getCallee();

            if ($callee instanceof Node\MemberExpression) {
                $object = $callee->getObject();
                $property = $callee->getProperty();

                if ($object instanceof Node\Identifier && $property instanceof Node\Identifier) {
                    return $object->getName() === $objectName && $property->getName() === $methodName;
                }
            }
        }

        return false;
    }

    /**
     * Extracts a message domain from the translator call.
     *
     * @param \SplFileInfo $file
     * @param \Peast\Syntax\Node\Expression[] $arguments
     *
     * @return string|null
     */
    private function extractId(SplFileInfo $file, array $arguments): ?string
    {
        if (!empty($arguments)) {
            $idNode = $arguments[self::ID_ARG];

            if (!($idNode instanceof Node\StringLiteral)) {
                $position = $idNode->getLocation()->getStart();

                $this->logger->error(sprintf(
                    'Could not extract id, expected string literal but got %s (in %s on line %d column %d).',
                    $idNode->getType(),
                    $file->getRealPath(),
                    $position->getLine(),
                    $position->getColumn()
                ));
            }

            return $idNode->getValue();
        }

        return null;
    }

    /**
     * Extracts a message domain from the translator call.
     *
     * @param \SplFileInfo $file
     * @param \Peast\Syntax\Node\Expression[] $arguments
     *
     * @return string|null
     */
    private function extractDomain(SplFileInfo $file, array $arguments): ?string
    {
        if (isset($arguments[self::DOMAIN_ARG])) {
            $domainNode = $arguments[self::DOMAIN_ARG];

            if (!($domainNode instanceof Node\StringLiteral)) {
                $position = $domainNode->getLocation()->getStart();

                $this->logger->error(sprintf(
                    'Could not extract domain, expected string literal but got %s (in %s on line %d column %d).',
                    $domainNode->getType(),
                    $file->getRealPath(),
                    $position->getLine(),
                    $position->getColumn()
                ));
            }

            return $domainNode->getValue();
        }

        return null;
    }

    /**
     * Extracts a message description from the translator call.
     *
     * @param \Peast\Syntax\Node\Expression[] $arguments
     *
     * @return string|null
     */
    private function extractDesc(array $arguments): ?string
    {
        if (!empty($arguments)) {
            foreach ($arguments[self::ID_ARG]->getLeadingComments() as $comment) {
                $annotations = $this->docParser->parse($comment->getText());
                if (!empty($annotations)) {
                    return $annotations[0]->text;
                }
            }
        }

        return null;
    }

    /**
     * Returns true if file is supported by extractor.
     *
     * @param \SplFileInfo $file
     *
     * @return bool
     */
    private function supports(SplFileInfo $file): bool
    {
        return '.js' === substr($file->getRealPath(), -3) && '.min.js' !== substr($file->getRealPath(), -7);
    }
}
