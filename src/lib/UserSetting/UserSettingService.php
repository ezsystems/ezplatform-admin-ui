<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UserSetting;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\UserPreferenceService;
use eZ\Publish\API\Repository\Values\UserPreference\UserPreferenceSetStruct;
use EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException;

/**
 * @internal
 */
class UserSettingService
{
    /** @var \eZ\Publish\API\Repository\UserPreferenceService */
    protected $userPreferenceService;

    /** @var \EzSystems\EzPlatformAdminUi\UserSetting\ValueDefinitionRegistry */
    protected $valueRegistry;

    /**
     * @param \eZ\Publish\API\Repository\UserPreferenceService $userPreferenceService
     * @param \EzSystems\EzPlatformAdminUi\UserSetting\ValueDefinitionRegistry $valueRegistry
     */
    public function __construct(
        UserPreferenceService $userPreferenceService,
        ValueDefinitionRegistry $valueRegistry
    ) {
        $this->userPreferenceService = $userPreferenceService;
        $this->valueRegistry = $valueRegistry;
    }

    /**
     * @param string $identifier
     * @param string $value
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentException
     */
    public function setUserSetting(string $identifier, string $value): void
    {
        $userPreferenceSetStructs = [
            new UserPreferenceSetStruct(['name' => $identifier, 'value' => $value]),
        ];

        $this->userPreferenceService->setUserPreference($userPreferenceSetStructs);
    }

    /**
     * @param string $identifier
     *
     * @return \EzSystems\EzPlatformAdminUi\UserSetting\UserSetting
     *
     * @throws \EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function getUserSetting(string $identifier): UserSetting
    {
        if (!$this->valueRegistry->hasValueDefinition($identifier)) {
            throw new InvalidArgumentException(
                '$identifier',
                sprintf("There is no ValueDefinition for '%s'. Did you register the service?", $identifier)
            );
        }

        $value = $this->valueRegistry->getValueDefinition($identifier);

        try {
            $userPreference = $this->userPreferenceService->getUserPreference($identifier);
            $userPreferenceValue = $userPreference->value;
        } catch (NotFoundException $e) {
            $userPreferenceValue = $value->getDefaultValue();
        }

        return $this->createUserSetting($identifier, $value, $userPreferenceValue);
    }

    /**
     * @param int $offset
     * @param int $limit
     *
     * @return array
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function loadUserSettings(int $offset = 0, int $limit = 25): array
    {
        $values = $this->valueRegistry->getValueDefinitions();
        /** @var \EzSystems\EzPlatformAdminUi\UserSetting\ValueDefinitionInterface[] $slice */
        $slice = \array_slice($values, $offset, $limit, true);

        $userPreferences = [];
        foreach ($slice as $identifier => $userSettingDefinition) {
            try {
                $userPreference = $this->userPreferenceService->getUserPreference($identifier);
                $userPreferenceValue = $userPreference->value;
            } catch (NotFoundException $e) {
                $userPreferenceValue = $userSettingDefinition->getDefaultValue();
            }
            $userPreferences[$identifier] = $userPreferenceValue;
        }

        return $this->createUserSettings($values, $userPreferences);
    }

    /**
     * @return int
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function countUserSettings(): int
    {
        // @todo fix as soon as UserPreferenceService has appropriate method
        return $this->userPreferenceService->loadUserPreferences(0, 1)->totalCount;
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\UserSetting\ValueDefinitionInterface[] $values
     * @param array $userPreferences
     *
     * @return \EzSystems\EzPlatformAdminUi\UserSetting\UserSetting[]
     */
    private function createUserSettings(array $values, array $userPreferences): array
    {
        $userSettings = [];

        foreach ($values as $identifier => $value) {
            $userSettings[] = $this->createUserSetting($identifier, $value, $userPreferences[$identifier]);
        }

        return $userSettings;
    }

    /**
     * @param string $identifier
     * @param \EzSystems\EzPlatformAdminUi\UserSetting\ValueDefinitionInterface $value
     * @param string $userPreferenceValue
     *
     * @return \EzSystems\EzPlatformAdminUi\UserSetting\UserSetting
     */
    private function createUserSetting(
        string $identifier,
        ValueDefinitionInterface $value,
        string $userPreferenceValue
    ): UserSetting {
        return new UserSetting([
            'identifier' => $identifier,
            'name' => $value->getName(),
            'description' => $value->getDescription(),
            'value' => $value->getDisplayValue($userPreferenceValue),
        ]);
    }
}
