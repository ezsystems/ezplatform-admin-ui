<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\TrashLocationStrategy;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\Location;
use EzSystems\EzPlatformAdminUi\Form\Type\InfoTextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Translation\TranslatorInterface;

class HasRelations implements TrashLocationStrategy
{
    const TRASH_WHEN_RELATION = 'trash_with_children';

    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \Symfony\Component\Translation\TranslatorInterface */
    private $translator;

    public function __construct(
        ContentService $contentService,
        TranslatorInterface $translator
    ) {
        $this->contentService = $contentService;
        $this->translator = $translator;
    }

    public function supports(Location $location): bool
    {
        $versionInfo = $this->contentService->loadVersionInfo($location->contentInfo);
        $relations = $this->contentService->loadRelations($versionInfo);
        $reverseRelations = $this->contentService->loadReverseRelations($location->contentInfo);

        return !empty($relations) || !empty($reverseRelations);
    }

    public function addOptions(FormInterface $form, Location $location): void
    {
        $versionInfo = $this->contentService->loadVersionInfo($location->contentInfo);
        $relations = $this->contentService->loadRelations($versionInfo);
        $reverseRelations = $this->contentService->loadReverseRelations($location->contentInfo);

        $translatorParameters = [
            '%content_name%' => $location->getContent()->getName(),
            '%relations%' => \count($relations),
            '%reverse_relations%' => \count($reverseRelations),
        ];

        $infoText = $form->get('info_text');
        $currentInfoText = $infoText->getConfig()->getOption('text_list');
        array_push(
            $currentInfoText,
            /** @Desc("Content '%content_name%' have %relations% relation and is used in %reverse_relations% other content.") */
            $this->translator->trans('trash_container.modal.message_relations', $translatorParameters, 'messages')
        );
        $form
            ->add('info_text', InfoTextType::class, [
                'text_list' => $currentInfoText,
            ]);

        $trashOptions = $form->get('trash_options');
        $currentChoices = $trashOptions->getConfig()->getOption('choices');
        array_push(
            $currentChoices,
            /** @Desc("Content '%content_name%' will no longer be avaialble under %reverse_relations% other content") */
            [$this->translator->trans('location_trash_form.trash_when_relation', $translatorParameters, 'forms') => self::TRASH_WHEN_RELATION]
        );

        $form
            ->add('trash_options', ChoiceType::class, [
                'expanded' => true,
                'multiple' => true,
                'choices' => $currentChoices,
            ]);
    }
}
