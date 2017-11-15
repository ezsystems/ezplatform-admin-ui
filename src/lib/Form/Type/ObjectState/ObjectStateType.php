<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\ObjectState;

use eZ\Publish\API\Repository\ObjectStateService;
use EzSystems\EzPlatformAdminUi\Form\DataTransformer\ObjectStateTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

class ObjectStateType extends AbstractType
{
    /** @var ObjectStateService */
    protected $objectStateService;

    /**
     * @param ObjectStateService $objectStateService
     */
    public function __construct(ObjectStateService $objectStateService)
    {
        $this->objectStateService = $objectStateService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new ObjectStateTransformer($this->objectStateService));
    }

    public function getParent()
    {
        return HiddenType::class;
    }
}
