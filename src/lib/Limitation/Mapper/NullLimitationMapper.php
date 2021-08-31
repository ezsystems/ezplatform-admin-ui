<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Limitation\Mapper;

use eZ\Publish\API\Repository\Values\User\Limitation;
use EzSystems\EzPlatformAdminUi\Limitation\LimitationFormMapperInterface;
use EzSystems\EzPlatformAdminUi\Limitation\LimitationValueMapperInterface;
use Symfony\Component\Form\FormInterface;

class NullLimitationMapper implements LimitationFormMapperInterface, LimitationValueMapperInterface
{
    /**
     * @var string
     */
    private $template;

    public function __construct($template)
    {
        $this->template = $template;
    }

    public function mapLimitationForm(FormInterface $form, Limitation $data)
    {
    }

    public function getFormTemplate()
    {
        return $this->template;
    }

    public function filterLimitationValues(Limitation $limitation)
    {
    }

    public function mapLimitationValue(Limitation $limitation)
    {
        return $limitation->limitationValues;
    }
}
