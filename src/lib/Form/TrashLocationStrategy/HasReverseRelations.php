<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\TrashLocationStrategy;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\Location;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Translation\TranslatorInterface;

class HasReverseRelations implements TrashLocationStrategy
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
        $reverseRelations = $this->contentService->loadReverseRelations($location->contentInfo);

        return !empty($reverseRelations);
    }

    public function addOptions(FormInterface $form, Location $location): void
    {
        $reverseRelations = $this->contentService->loadReverseRelations($location->contentInfo);

        $translatorParameters = [
            '%content_name%' => $location->getContent()->getName(),
            '%reverse_relations%' => \count($reverseRelations),
        ];

        $form
            ->add('has_reverse_relation', ChoiceType::class, [
                'expanded' => true,
                'multiple' => true,
                'label' =>
                    /** @Desc("Conflict with Reverse Relations") */
                    $this->translator->trans('form.has_reverse_relation.label', [], 'forms'),
                'choices' =>
                    /** @Desc("'%content_name%' will not be available for its reverse relation(s)") */
                    [$this->translator->trans('location_trash_form.trash_when_relation', $translatorParameters, 'forms') => self::TRASH_WHEN_RELATION],
                'help_multiline' => [
                    /** @Desc("'%content_name%' is in use by %reverse_relations% content item(s). It is recommended to remove all reverse relations before deleting the content item.") */
                    $this->translator->trans('trash_container.modal.message_relations', $translatorParameters),
                ],
            ]);
    }
}
