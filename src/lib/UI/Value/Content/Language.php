<?php

declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\UI\Value\Content;

use eZ\Publish\API\Repository\Values\Content\Language as APILanguage;

/**
 * Extends original value object in order to provide additional fields.
 * Takes a standard language instance and retrieves properties from it in addition to the provided properties.
 */
class Language extends APILanguage
{
    /**
     * Is main language.
     *
     * @var bool
     */
    protected $main;

    /**
     * User can remove.
     *
     * @var bool
     */
    protected $userCanRemove;

    /**
     * @param APILanguage $language
     * @param array $properties
     */
    public function __construct(APILanguage $language, array $properties = [])
    {
        parent::__construct(get_object_vars($language) + $properties);
    }

    /**
     * Can delete translation.
     *
     * @return bool
     */
    public function canDelete(): bool
    {
        return !$this->main && $this->userCanRemove;
    }
}
