<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\Helper;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Element\TraversableElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\MinkExtension\Context\MinkContext;
use Exception;
use WebDriver\Exception\ElementNotVisible;

class UtilityContext extends MinkContext
{
    /**
     * Waits until element is visible. If it does not appear throws exception.
     *
     * @param string $cssSelector
     * @param int $timeout
     *
     * @throws Exception when element not found or is not visible
     */
    public function waitUntilElementIsVisible(string $cssSelector, int $timeout = 5, TraversableElement $baseElement = null): void
    {
        try {
            $this->waitUntil($timeout, function () use ($cssSelector, $baseElement) {
                $baseElement = $baseElement ?? $this->getSession()->getPage();

                $element = $baseElement->find('css', $cssSelector);

                return isset($element) && $element->isVisible();
            });
        } catch (Exception $e) {
            throw new ElementNotVisible(sprintf('Element with selector: %s was not found', $cssSelector));
        }
    }

    /**
     * Adpted Mink find function combined with a spin function
     * to find all element with a given css selector that might still be loading.
     *
     * @param   string      $locator        css selector for the element
     * @param   TraversableElement|null $baseElement    base Mink node element from where the find should be called
     *
     * @return  NodeElement[]
     */
    public function findAllElements(string $locator, TraversableElement $baseElement = null): array
    {
        $baseElement = $baseElement ?? $this->getSession()->getPage();

        $elements = $this->waitUntil(10,
            function () use ($locator, $baseElement) {
                $elements = $baseElement->findAll('css', $locator);
                foreach ($elements as $element) {
                    // An exception may be thrown if the element is not valid/attached to DOM.
                    $element->getValue();
                }

                return $elements;
            }
        );

        return $elements;
    }

    /**
     * Finds an HTML element by class and the text value and returns it. Search can be narrowed to children of baseElement.
     *
     * @param string $text Text value of the element
     * @param string $selector CSS selector of the element
     * @param string $textSelector Extra CSS selector for text of the element
     * @param TraversableElement|null $baseElement
     *
     * @return NodeElement|null
     */
    public function getElementByText(string $text, string $selector, string $textSelector = null, TraversableElement $baseElement = null): ?NodeElement
    {
        $baseElement = $baseElement ?? $this->getSession()->getPage();

        $elements = $this->findAllElements($selector, $baseElement);
        foreach ($elements as $element) {
            if ($textSelector !== null) {
                try {
                    $elementText = $this->findElement($textSelector, 10, $element)->getText();
                } catch (\Exception $e) {
                    continue;
                }
            } else {
                $elementText = $element->getText();
            }
            if ($elementText === $text) {
                return $element;
            }
        }

        return null;
    }

    /**
     * Finds an HTML element by class and the fragment of text value and returns it. Search can be narrowed to children of baseElement.
     *
     * @param string $text Fragment of text value of the element
     * @param string $selector CSS selector of the element
     * @param string $textSelector Extra CSS selector for text of the element
     * @param TraversableElement|null $baseElement
     *
     * @return NodeElement|null
     */
    public function getElementByTextFragment(string $textFragment, string $selector, string $textSelector = null, TraversableElement $baseElement = null): ?NodeElement
    {
        $baseElement = $baseElement ?? $this->getSession()->getPage();

        $elements = $this->findAllElements($selector, $baseElement);
        foreach ($elements as $element) {
            if ($textSelector !== null) {
                try {
                    $elementText = $this->findElement($textSelector, 10, $element)->getText();
                } catch (\Exception $e) {
                    continue;
                }
            } else {
                $elementText = $element->getText();
            }
            if (strpos($elementText, $textFragment) !== false) {
                return $element;
            }
        }

        return null;
    }

    /**
     * Finds an HTML element by class and the text value and returns it's position in order. Search can be narrowed to children of baseElement.
     *
     * @param string $text Text value of the element
     * @param string $selector CSS selector of the element
     * @param string $textSelector Extra CSS selector for text of the element
     * @param TraversableElement|null $baseElement
     *
     * @return int
     */
    public function getElementPositionByText(string $text, string $selector, string $textSelector = null, TraversableElement $baseElement = null): int
    {
        $baseElement = $baseElement ?? $this->getSession()->getPage();
        $counter = 0;

        $elements = $this->findAllElements($selector, $baseElement);
        foreach ($elements as $element) {
            ++$counter;
            if ($textSelector !== null) {
                try {
                    $elementText = $this->findElement($textSelector, 10, $element)->getText();
                } catch (\Exception $e) {
                    continue;
                }
            } else {
                $elementText = $element->getText();
            }
            if ($elementText === $text) {
                return $counter;
            }
        }

        return 0;
    }

    /**
     * Waits no longer than specified timeout for the given condition to be true.
     *
     * @param int $timeoutSeconds Timeout
     * @param callable Condition to verify
     * @param bool $throwOnFailure Whether Exception should be thrown when timeout is exceeded
     *
     * @return mixed
     *
     * @throws Exception If $throwOnFailure is true and timeout exceeded
     */
    public function waitUntil(int $timeoutSeconds, callable $callback, bool $throwOnFailure = true)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('Given callback is not a valid callable');
        }

        $start = time();
        $end = $start + $timeoutSeconds;

        $lastInternalExceptionMessage = '';

        do {
            try {
                $result = $callback($this);

                if ($result) {
                    return $result;
                }
            } catch (Exception $e) {
                $lastInternalExceptionMessage = $e->getMessage();
            }
            usleep(250 * 1000);
        } while (time() < $end);

        if ($throwOnFailure) {
            throw new Exception('Spin function did not return in time. Last internal exception:' . $lastInternalExceptionMessage);
        }
    }

    /**
     * Adopted Mink find function to find one element that might still be loading.
     *
     * @param string $selector CSS selector for the element
     * @param int $timeout
     * @param TraversableElement|null $baseElement Element from which the DOM will be searched
     *
     * @return NodeElement Searched element
     *
     * @throws ElementNotFoundException
     */
    public function findElement(string $selector, int $timeout = 5, TraversableElement $baseElement = null): NodeElement
    {
        $baseElement = $baseElement ?? $this->getSession()->getPage();

        try {
            return $this->waitUntil($timeout,
                function () use ($selector, $baseElement) {
                    return $baseElement->find('css', $selector);
                });
        } catch (Exception $e) {
            throw new ElementNotFoundException($this->getSession()->getDriver(), null, 'css', $selector);
        }
    }

    /**
     * Filters an array of elements and returns the visible ones.
     *
     * @param NodeElement[] $elements
     *
     * @return NodeElement[]
     */
    public function getVisibleElements(array $elements): array
    {
        return array_filter($elements, function ($element) { return $element->isVisible(); });
    }

    /**
     * Checks the visibility of a HTML element found by class.
     *
     * @param string $locator CSS locator of the element
     * @param int $timeout Number of seconds to wait until the element appears
     *
     * @return bool
     */
    public function isElementVisible(string $locator, int $timeout = 5): bool
    {
        try {
            $element = $this->findElement($locator, $timeout);

            return isset($element) && $element->isVisible();
        } catch (ElementNotFoundException $e) {
            return false;
        }
    }

    public function waitUntilElementDisappears(string $cssSelector, int $timeout): void
    {
        try {
            $this->waitUntil($timeout, function () use ($cssSelector, $timeout) {
                return !$this->isElementVisible($cssSelector, 1);
            });
        } catch (Exception $e) {
            throw new Exception(sprintf('Element with selector: %s did not disappear in %d seconds.', $cssSelector, $timeout));
        }
    }

    /**
     * Uploads file from location stored in 'files_path' to the disc on remote browser machine. Mink require uploaded file to be zip archive.
     *
     * @param string $localFileName
     *
     * @return string
     */
    public function uploadFileToRemoteSpace(string $localFileName): string
    {
        if (!preg_match('#[\w\\\/\.]*\.zip$#', $localFileName)) {
            throw new \InvalidArgumentException('Zip archive required to upload to remote browser machine.');
        }

        $localFile = sprintf('%s%s', $this->getMinkParameter('files_path'), $localFileName);

        return $this->getSession()->getDriver()->getWebDriverSession()->file([
            'file' => base64_encode(file_get_contents($localFile)),
        ]);
    }

    private function isDraggingLibraryLoaded(): bool
    {
        return $this->getSession()->getDriver()->evaluateScript("typeof(dragMock) !== 'undefined'");
    }

    public function moveWithHover(string $startExpression, string $hoverExpression, string $placeholderExpression): void
    {
        $this->loadDraggingLibrary();

        $movingScript = sprintf('dragMock.dragStart(%s).dragOver(%s).delay(100).drop(%s);', $startExpression, $hoverExpression, $placeholderExpression);
        $this->getSession()->getDriver()->executeScript($movingScript);
    }

    private function loadDraggingLibrary(): void
    {
        if ($this->isDraggingLibraryLoaded()) {
            return;
        }

        $script = file_get_contents(__DIR__ . '/../lib/drag-mock.js');
        $this->getSession()->getDriver()->executeScript($script);
        $this->waitUntil(10, function () {
            return $this->isDraggingLibraryLoaded();
        });
    }
}
