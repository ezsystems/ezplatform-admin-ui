<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\ContentType;

use eZ\Publish\Core\Helper\FieldsGroups\FieldsGroupsList;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class FieldGroupChoiceType extends AbstractType
{
    /** @var \eZ\Publish\Core\Helper\FieldsGroups\FieldsGroupsList */
    private $groupsList;

    public function __construct(FieldsGroupsList $groupsList)
    {
        $this->groupsList = $groupsList;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => array_flip($this->groupsList->getGroups()),
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
