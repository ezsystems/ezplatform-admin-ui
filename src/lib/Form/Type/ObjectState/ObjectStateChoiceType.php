<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\ObjectState;

use eZ\Publish\API\Repository\ObjectStateService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ObjectStateChoiceType extends AbstractType
{
    /** @var \eZ\Publish\API\Repository\ObjectStateService */
    protected $objectStateService;

    /**
     * @param \eZ\Publish\API\Repository\ObjectStateService $objectStateService
     */
    public function __construct(ObjectStateService $objectStateService)
    {
        $this->objectStateService = $objectStateService;
    }

    public function getParent()
    {
        return ChoiceType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'choice_loader' => new CallbackChoiceLoader(function () {
                    $objectStates = [];
                    $objectStateGroups = $this->objectStateService->loadObjectStateGroups();
                    foreach ($objectStateGroups as $objectStateGroup) {
                        $objectStates[$objectStateGroup->identifier] = $this->objectStateService->loadObjectStates($objectStateGroup);
                    }

                    return $objectStates;
                }),
                'choice_label' => 'name',
                'choice_name' => 'identifier',
                'choice_value' => 'id',
            ]);
    }
}
