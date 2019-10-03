<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\TrashLocationOptionProvider;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\Location;
use EzSystems\EzPlatformAdminUi\Specification\Location\HasChildren as HasChildrenSpec;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Translation\TranslatorInterface;

final class HasChildren implements TrashLocationOptionProvider
{
    const TRASH_WITH_CHILDREN = 'trash_with_children';

    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    /** @var \Symfony\Component\Translation\TranslatorInterface */
    private $translator;

    public function __construct(LocationService $locationService, TranslatorInterface $translator)
    {
        $this->locationService = $locationService;
        $this->translator = $translator;
    }

    public function supports(Location $location): bool
    {
        return (new HasChildrenSpec($this->locationService))->isSatisfiedBy($location);
    }

    public function addOptions(FormInterface $form, Location $location): void
    {
        $locationChildren = $this->locationService->loadLocationChildren($location);

        $translatorParameters = [
            '%children_count%' => $locationChildren->totalCount,
            '%content_name%' => $location->getContent()->getName(),
        ];

        $form
            ->add('has_children', ChoiceType::class, [
                'expanded' => true,
                'multiple' => true,
                'label' =>
                    /** @Desc("Sub-items") */
                    $this->translator->trans('form.has_children.label', [], 'forms'),
                'choices' =>
                    /** @Desc("Send to trash %children_count% content item(s) of this location") */
                    [$this->translator->trans('location_trash_form.trash_container', $translatorParameters, 'forms') => self::TRASH_WITH_CHILDREN],
                'help_multiline' => [
                    /** @Desc("Sending '%content_name%' to trash will also send its sub-items to trash. To confirm, check below:") */
                    $this->translator->trans('trash_container.modal.message_main', $translatorParameters, 'messages'),
                ],
            ]);
    }
}
