<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\UI\Config\Provider;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\API\Repository\Values\User\User as ApiUser;
use eZ\Publish\Core\MVC\Symfony\Security\UserInterface;
use EzSystems\EzPlatformAdminUi\UI\Config\ProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Provides information about current user with resolved profile picture.
 */
class User implements ProviderInterface
{
    /** @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface */
    private $tokenStorage;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /**
     * @param \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface $tokenStorage
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
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
     * @param \eZ\Publish\API\Repository\Values\User\User $user
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Field|null
     */
    private function resolveProfilePictureField(ApiUser $user): ?Field
    {
        $contentType = $user->getContentType();
        foreach ($user->getFields() as $field) {
            $fieldDef = $contentType->getFieldDefinition($field->fieldDefIdentifier);

            if ('ezimage' === $fieldDef->fieldTypeIdentifier) {
                return $field;
            }
        }

        return null;
    }
}
