<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Templating\Twig;

use EzSystems\EzPlatformAdminUi\UserSetting\UserSettingService;
use EzSystems\EzPlatformAdminUi\UI\Config\ConfigWrapper;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use ProxyManager\Proxy\LazyLoadingInterface;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

/**
 * @internal
 *
 * @todo provide extensibility to map selected settings
 */
class UserPreferencesGlobalExtension extends AbstractExtension implements GlobalsInterface
{
    /** @var \EzSystems\EzPlatformAdminUi\UserSetting\UserSettingService */
    protected $userSettingService;

    /**
     * @param \EzSystems\EzPlatformAdminUi\UserSetting\UserSettingService $userSettingService
     */
    public function __construct(
        UserSettingService $userSettingService
    ) {
        $this->userSettingService = $userSettingService;
    }

    /**
     * @return array
     *
     * @throws \EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function getGlobals(): array
    {
        return [
            'ez_user_settings' => $this->createConfigWrapper(),
        ];
    }

    /**
     * Create lazy loaded configuration.
     *
     * @return \EzSystems\EzPlatformAdminUi\UI\Config\ConfigWrapper
     */
    private function createConfigWrapper(): ConfigWrapper
    {
        $factory = new LazyLoadingValueHolderFactory();
        $initializer = function (&$wrappedObject, LazyLoadingInterface $proxy, $method, array $parameters, &$initializer) {
            $initializer = null;
            $wrappedObject = new ConfigWrapper($this->getUserSettings());

            return true;
        };

        return $factory->createProxy(ConfigWrapper::class, $initializer);
    }

    /**
     * @return array
     *
     * @throws \EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    private function getUserSettings(): array
    {
        return [
            'timezone' => $this->getTimezoneValue(),
            'character_counter' => $this->getCharacterCounterValue(),
        ];
    }

    /**
     * @return string
     *
     * @throws \EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    private function getTimezoneValue(): string
    {
        return $this->userSettingService->getUserSetting('timezone')->value;
    }

    /**
     * @return string
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    private function getCharacterCounterValue(): string
    {
        return $this->userSettingService->getUserSetting('character_counter')->value;
    }
}
