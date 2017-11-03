<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Tests\Behat;

use Behat\Mink\Element\NodeElement;
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
     * Waits no longer than specified timeout for the given condition to be true.
     *
     * @param int $timeoutSeconds Timeout
     * @param $callback Condition to verify
     * @param bool $throwOnFailure Whether Exception should be thrown when timeout is exceeded
     *
     * @return mixed
     *
     * @throws Exception If $throwOnFailure is true and timeout exceeded
     */
    public function waitUntil(int $timeoutSeconds, $callback, $throwOnFailure = true)
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
     *
     * @return \Behat\Mink\Element\NodeElement Searched element
     */
    public function findElement(string $selector, int $timeout): ?NodeElement
    {
        return $this->waitUntil($timeout,
            function () use ($selector) {
                return $this->getSession()->getPage()->find('css', $selector);
            });
    }
}
