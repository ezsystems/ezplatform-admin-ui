<?php

namespace EzPlatformAdminUi\Form\Type\UniversalDiscoveryWidget;

use EzPlatformAdminUi\Form\Type\Content\LocationType;
use EzPlatformAdminUi\Form\Type\Content\SectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UniversalDiscoveryWidgetType extends AbstractType
{
    const TAB_BROWSE = 'browse';
    const TAB_SEARCH = 'search';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('location', LocationType::class, [
                'attr' => ['hidden' => true],
                'multiple' => $options['multiple'],
            ])
            ->add('select_content', ButtonType::class, [
                'label' => $options['label'],
                'label_format' => $options['label_format'],
                'attr' => $options['attr'],
            ])
            ->addModelTransformer($this->getDataTransformer());
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $selectContentButtonView = $view->offsetGet('select_content');

        if (!empty($options['title'])) {
            $selectContentButtonView->vars['attr']['data-title'] = $options['title'];
        }

        if ($options['multiple']) {
            $selectContentButtonView->vars['attr']['data-multiple'] = strval($options['multiple']);
        }

        if (!empty($options['active_tab'])) {
            $selectContentButtonView->vars['attr']['data-active-tab'] = $options['active_tab'];
        }

        if ($options['initial_location_id']) {
            $selectContentButtonView->vars['attr']['data-initial-location-id'] = $options['initial_location_id'];
        }

        if (!isset($selectContentButtonView->vars['attr']['class'])) {
            $selectContentButtonView->vars['attr']['class'] = '';
        }

        $selectContentButtonView->vars['attr']['class'] = trim($selectContentButtonView->vars['attr']['class'].' btn--open-udw');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'title' => '',
            'multiple' => false,
            'active_tab' => static::TAB_BROWSE,
            'initial_location_id' => null,
            'return_content_info' => false,
        ]);

        $resolver->setAllowedValues('active_tab', [static::TAB_BROWSE, static::TAB_SEARCH]);
        $resolver->setAllowedTypes('multiple', 'boolean');
        $resolver->setAllowedTypes('return_content_info', 'boolean');
        $resolver->setAllowedTypes('title', 'string');
        $resolver->setAllowedTypes('initial_location_id', ['int', 'null']);
    }

    public function getBlockPrefix()
    {
        return 'ezsystems_ezplatform_type_udw';
    }

    /**
     * @return DataTransformerInterface
     */
    private function getDataTransformer(): DataTransformerInterface
    {
        return new CallbackTransformer(
            function ($value) {
                if (null === $value) {
                    return null;
                }

                return ['location' => implode(',', $value)];
            },
            function ($value) {
                if (is_array($value) && array_key_exists('location', $value)) {
                    return $value['location'];
                }

                return null;
            }
        );
    }
}
