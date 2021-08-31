<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\Role;

use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\Section;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class RoleAssignmentCreateData implements TranslationContainerInterface
{
    const LIMITATION_TYPE_NONE = 'none';
    const LIMITATION_TYPE_SECTION = 'section';
    const LIMITATION_TYPE_LOCATION = 'location';

    /** @var \eZ\Publish\API\Repository\Values\User\UserGroup[] */
    private $groups;

    /** @var \eZ\Publish\API\Repository\Values\User\User[] */
    private $users;

    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Section[]
     *
     * @Assert\Expression(
     *     "this.getLimitationType() != 'section' or (this.getLimitationType() == 'section' and value != [])",
     *     message="validator.define_subtree_or_section_limitation"
     * )
     */
    private $sections;

    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Location[]
     *
     * @Assert\Expression(
     *     "this.getLimitationType() != 'location' or (this.getLimitationType() == 'location' and value != [])",
     *     message="validator.define_subtree_or_section_limitation"
     * )
     */
    private $locations;

    /**
     * @var string
     *
     * @Assert\NotNull()
     * @Assert\Choice({
     *     RoleAssignmentCreateData::LIMITATION_TYPE_NONE,
     *     RoleAssignmentCreateData::LIMITATION_TYPE_SECTION,
     *     RoleAssignmentCreateData::LIMITATION_TYPE_LOCATION
     * })
     */
    private $limitationType;

    /**
     * @param \eZ\Publish\API\Repository\Values\User\UserGroup[] $groups
     * @param \eZ\Publish\API\Repository\Values\User\User[] $users
     * @param \eZ\Publish\API\Repository\Values\Content\Section[] $sections
     * @param \eZ\Publish\API\Repository\Values\Content\Location[] $locations
     * @param string $limitationType
     */
    public function __construct(
        array $groups = [],
        array $users = [],
        array $sections = [],
        array $locations = [],
        $limitationType = self::LIMITATION_TYPE_NONE
    ) {
        $this->groups = $groups;
        $this->users = $users;
        $this->sections = $sections;
        $this->locations = $locations;
        $this->limitationType = $limitationType;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\User\UserGroup[]
     */
    public function getGroups(): ?array
    {
        return $this->groups;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\User\UserGroup[] $groups
     *
     * @return self
     */
    public function setGroups(array $groups): self
    {
        $this->groups = $groups;

        return $this;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\User\User[]
     */
    public function getUsers(): ?array
    {
        return $this->users;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\User\User[] $users
     *
     * @return self
     */
    public function setUsers(array $users): self
    {
        $this->users = $users;

        return $this;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Section[]
     */
    public function getSections(): ?array
    {
        return $this->sections;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Section[] $sections
     *
     * @return self
     */
    public function setSections(array $sections): self
    {
        $this->sections = $sections;

        return $this;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Location[]
     */
    public function getLocations(): ?array
    {
        return $this->locations;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location[] $locations
     *
     * @return self
     */
    public function setLocations(array $locations): self
    {
        $this->locations = $locations;

        return $this;
    }

    /**
     * @return string
     */
    public function getLimitationType(): string
    {
        return $this->limitationType;
    }

    /**
     * @param string $limitationType
     *
     * @return self
     */
    public function setLimitationType(string $limitationType): self
    {
        $this->limitationType = $limitationType;

        return $this;
    }

    /**
     * @param \Symfony\Component\Validator\Context\ExecutionContextInterface $context
     * @param $payload
     *
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if (empty($this->getUsers()) && empty($this->getGroups())) {
            $context->buildViolation(
            'validator.assign_users_or_groups')
                ->setTranslationDomain('role')
                ->addViolation();
        }
    }

    public static function getTranslationMessages()
    {
        return [
            Message::create('validator.assign_users_or_groups', 'role')
                ->setDesc('Assign User(s) and/or Group(s) to the Role'),
            Message::create('validator.define_subtree_or_section_limitation', 'validators')
                ->setDesc('Define a Subtree or Section limitation'),
        ];
    }
}
