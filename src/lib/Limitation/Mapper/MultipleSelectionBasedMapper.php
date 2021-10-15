<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\AdminUi\Limitation\Mapper;

use eZ\Publish\API\Repository\Values\User\Limitation;
use Ibexa\AdminUi\Limitation\LimitationFormMapperInterface;
use Ibexa\AdminUi\Translation\Extractor\LimitationTranslationExtractor;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;

/**
 * Base class for mappers based on multiple selection.
 */
abstract class MultipleSelectionBasedMapper implements LimitationFormMapperInterface
{
    /**
     * Form template to use.
     *
     * @var string
     */
    private $template;

    public function mapLimitationForm(FormInterface $form, Limitation $data)
    {
        $options = $this->getChoiceFieldOptions() + [
            'multiple' => true,
            'label' => LimitationTranslationExtractor::identifierToLabel($data->getIdentifier()),
            'required' => false,
        ];
        $choices = $this->getSelectionChoices();
        asort($choices, SORT_NATURAL | SORT_FLAG_CASE);
        $options['choices'] = array_flip($choices);
        $form->add('limitationValues', ChoiceType::class, $options);
    }

    /**
     * Returns value choices to display, as expected by the "choices" option from Choice field.
     *
     * @return array
     */
    abstract protected function getSelectionChoices();

    /**
     * Returns custom options.
     *
     * @return array
     */
    protected function getChoiceFieldOptions()
    {
        return [];
    }

    public function setFormTemplate($template)
    {
        $this->template = $template;
    }

    public function getFormTemplate()
    {
        return $this->template;
    }

    public function filterLimitationValues(Limitation $limitation)
    {
    }
}

class_alias(MultipleSelectionBasedMapper::class, 'EzSystems\EzPlatformAdminUi\Limitation\Mapper\MultipleSelectionBasedMapper');
