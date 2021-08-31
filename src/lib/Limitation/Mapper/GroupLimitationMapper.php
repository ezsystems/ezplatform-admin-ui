<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Limitation\Mapper;

use eZ\Publish\API\Repository\Values\User\Limitation;
use EzSystems\EzPlatformAdminUi\Limitation\LimitationValueMapperInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class GroupLimitationMapper extends MultipleSelectionBasedMapper implements LimitationValueMapperInterface
{
    /**
     * @var \Symfony\Contracts\Translation\TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    protected function getSelectionChoices()
    {
        return [
            1 => $this->translator->trans(/** @Desc("Self") */ 'policy.limitation.group.self', [], 'ezplatform_content_forms_role'),
        ];
    }

    public function mapLimitationValue(Limitation $limitation)
    {
        return [
            $this->translator->trans(/** @Desc("Self") */ 'policy.limitation.group.self', [], 'ezplatform_content_forms_role'),
        ];
    }
}
