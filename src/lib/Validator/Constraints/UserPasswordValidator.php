<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Validator\Constraints;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Exceptions\InvalidArgumentException;
use eZ\Publish\API\Repository\Values\User\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException as ValidatorConstraintDefinitionException;

/**
 * Will check if logged user and password are match.
 */
class UserPasswordValidator extends ConstraintValidator
{
    /** @var UserService */
    private $userService;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    /**
     * @param UserService $userService
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(UserService $userService, TokenStorageInterface $tokenStorage)
    {
        $this->userService = $userService;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Checks if the passed password exists for logged user.
     *
     * @param string $password The password that should be validated
     * @param Constraint|UserPassword $constraint The constraint for the validation
     *
     * @throws ValidatorConstraintDefinitionException
     */
    public function validate($password, Constraint $constraint)
    {
        if (null === $password || '' === $password) {
            $this->context->addViolation($constraint->message);

            return;
        }

        $user = $this->tokenStorage->getToken()->getUser()->getAPIUser();

        if (!$user instanceof User) {
            throw new ConstraintDefinitionException('The User object must implement the UserReference interface.');
        }

        try {
            $this->userService->loadUserByCredentials($user->login, $password);
        } catch (NotFoundException | InvalidArgumentException $e) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
