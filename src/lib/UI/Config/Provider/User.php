<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\UI\Config\Provider;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\API\Repository\Values\User\User as ApiUser;
use EzSystems\EzPlatformAdminUi\UI\Config\ProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use eZ\Publish\Core\MVC\Symfony\Security\UserInterface;

/**
 * Provides information about current user with resolved profile picture.
 */
class User implements ProviderInterface
{
    /** @var TokenStorageInterface */
    private $tokenStorage;

    /** @var ContentTypeService */
    private $contentTypeService;

    /**
     * @param TokenStorageInterface $tokenStorage
     * @param ContentTypeService $contentTypeService
     */
    public function __construct(
        TokenStorageInterface $tokenStorage,
        ContentTypeService $contentTypeService
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->contentTypeService = $contentTypeService;
    }

    /**
     * Returns configuration structure compatible with PlatformUI.
     *
     * @return array
     */
    public function getConfig(): array
    {
        $config = ['user' => null, 'profile_picture_field' => null];

        $token = $this->tokenStorage->getToken();
        if (!$token instanceof TokenInterface) {
            return $config;
        }

        $user = $token->getUser();
        if ($user instanceof UserInterface) {
            $apiUser = $user->getAPIUser();
            $config['user'] = $apiUser;
            $config['profile_picture_field'] = $this->resolveProfilePictureField($apiUser);
        }

        return $config;
    }

    /**
     * Returns first occurrence of an `ezimage` fieldtype.
     *
     * @param ApiUser $user
     *
     * @return Field|null
     */
    private function resolveProfilePictureField(ApiUser $user): ?Field
    {
        try {
            $contentType = $this->contentTypeService->loadContentType($user->contentInfo->contentTypeId);
        } catch (\Exception $e) {
            return null;
        }

        foreach ($user->getFields() as $field) {
            $fieldDef = $contentType->getFieldDefinition($field->fieldDefIdentifier);

            if ('ezimage' === $fieldDef->fieldTypeIdentifier) {
                return $field;
            }
        }

        return null;
    }
}
