<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\TrashLocationStrategy;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\Location;
use EzSystems\EzPlatformAdminUi\Form\Type\InfoTextType;
use EzSystems\EzPlatformAdminUi\Specification\Location\HasChildren as HasChildrenSpec;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Translation\TranslatorInterface;

class HasChildren implements TrashLocationStrategy
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

        $infoText = $form->get('info_text');
        $currentInfoText = $infoText->getConfig()->getOption('text_list');
        array_push(
            $currentInfoText,
            /** @Desc("Deleting %content_name% will also delete its sub-items, under its location(s). To confirm, please check below") */
            $this->translator->trans('trash_container.modal.message_main', $translatorParameters, 'messages')
        );
        $form
            ->add('info_text', InfoTextType::class, [
                'text_list' => $currentInfoText,
            ]);

        $trashOptions = $form->get('trash_options');
        $currentChoices = $trashOptions->getConfig()->getOption('choices');
        array_push(
            $currentChoices,
            /** @Desc("%children_count% content items under location %content_name%") */
            [$this->translator->trans('location_trash_form.trash_container', $translatorParameters, 'forms') => self::TRASH_WITH_CHILDREN]
        );

        $form
            ->add('trash_options', ChoiceType::class, [
                'expanded' => true,
                'multiple' => true,
                'choices' => $currentChoices,
            ]);
    }
}
