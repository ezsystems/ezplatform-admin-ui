<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Element\TraversableElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\MinkExtension\Context\MinkContext;
use Exception;
use WebDriver\Exception\ElementNotVisible;

class UtilityContext extends MinkContext
{
    use StudioUtility;

    /**
     * Waits until element is visible. If it does not appear throws exception.
     *
     * @param string $cssSelector
     * @param int $timeout
     *
     * @throws Exception when element not found
     */
    public function waitUntilElementIsVisible(string $cssSelector, int $timeout = 5): void
    {
        try {
            $this->waitUntil($timeout, function () use ($cssSelector) {
                $element = $this->getSession()->getPage()->find('css', $cssSelector);

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
     * @param   TraversableElement $baseElement    base Mink node element from where the find should be called
     *
     * @return  NodeElement[]
     */
    public function findAllWithWait(string $locator, TraversableElement $baseElement = null): array
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

        $elements = $this->findAllWithWait($selector, $baseElement);
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

        do {
            try {
                $result = $callback($this);

                if ($result) {
                    return $result;
                }
            } catch (Exception $e) {
            }
            usleep(250 * 1000);
        } while (time() < $end);

        if ($throwOnFailure) {
            throw new Exception('Spin function did not return in time');
        }
    }

    /**
     * Adopted Mink find function to find one element that might still be loading.
     *
     * @param string $selector CSS selector for the element
     * @param int $timeout
     * @param TraversableElement|null $baseElement Element from which the DOM will be searched
     *
     * @return NodeElement|null Searched element
     *
     * @throws ElementNotFoundException
     */
    public function findElement(string $selector, int $timeout = 5, TraversableElement $baseElement = null): ?NodeElement
    {
        $baseElement = $baseElement ?? $this->getSession()->getPage();

        try {
            return $this->waitUntil($timeout,
                function () use ($selector, $baseElement) {
                    return $baseElement->find('css', $selector);
                });
        } catch (Exception $e) {
            throw new ElementNotFoundException($this->getSession()->getDriver());
        }
    }

    /**
     * Checks the visibility of a HTML element found by class.
     *
     * @param string $locator CSS locator of the element
     * @param int $timeout Number of seconds to wait until the element appears
     *
     * @return bool
     */
    public function checkVisibilityByClass(string $locator, int $timeout = 5): bool
    {
        try {
            $element = $this->findElement($locator, $timeout);

            return isset($element) && $element->isVisible();
        } catch (ElementNotFoundException $e) {
            return false;
        }
    }
}
